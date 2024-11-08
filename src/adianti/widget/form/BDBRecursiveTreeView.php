<?php

namespace Mycomponent\Widget\Form;

use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Database\TConnection;
use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;
use Adianti\Database\TRepository;
use Adianti\Database\TTransaction;
use Exception;
use Mad\Widget\Form\BTreeView;

class BDBRecursiveTreeView extends BTreeView
{
    private $model;
    private $key;
    private $key_value;
    private $recusive_column;
    private $ordercolumn;
    private $criteria;

    public function __construct($name, $database, $model, $key, $value, $recusive_column, $ordercolumn = NULL, TCriteria $criteria = NULL)
    {
        parent::__construct($name);

        $this->database = $database;
        $this->model = $model;
        $this->key = $key;
        $this->key_value = $value;
        $this->recusive_column = $recusive_column;
        $this->ordercolumn = $ordercolumn;
        $this->criteria = $criteria;

        $this->setItems($this->getItemsFromModel());
    }

    private function makeGroups($object)
    {
        return $this->recursiveGroup($object, []);
    }

    private function recursiveGroup($object, $arrayItem, $isChild = false)
    {
        $groupCriteria = clone $this->criteria;
        $groupCriteria->add(new TFilter($this->recusive_column, '=', $object->{$this->key}));

        $itemsGroup = new TRepository($this->model);
        $itemsGroup = $itemsGroup->load($groupCriteria);

        $key = (isset($object->{$this->key})) ? $object->{$this->key} : $object->render($this->key);
        $value = (isset($object->{$this->key_value})) ? $object->{$this->key_value} : $object->render($this->key_value);

        if (empty($itemsGroup) && $isChild)
        {
            $arrayItem["btreekey_{$key}"] = [$value, $object];
            return $arrayItem;
        }
        elseif(empty($itemsGroup))
        {
            $arrayItem["btreekey_{$key}"] = ['label' => $value, 'object' => $object, 'items' => []];

            return $arrayItem;
        }

        $items = [];

        foreach($itemsGroup as $item)
        {
            if(!empty($items["btreekey_{$key}"]['object']))
            {
                $items = array_merge_recursive($items, ["btreekey_{$key}" => ['items' => $this->recursiveGroup($item, [], true)]]);
            }
            else
            {
                $items = array_merge_recursive($items, ["btreekey_{$key}" => ['label' => $value, 'object' => $object, 'items' => $this->recursiveGroup($item, [], true)]]);
            }
        }

        return $items;
    }

    public function getItemsFromModel()
    {
        $items = [];

        if (empty($this->database))
        {
            throw new Exception(AdiantiCoreTranslator::translate('The parameter (^1) of ^2 is required', 'database', __CLASS__));
        }

        if (empty($this->model))
        {
            throw new Exception(AdiantiCoreTranslator::translate('The parameter (^1) of ^2 is required', 'model', __CLASS__));
        }

        if (empty($this->key))
        {
            throw new Exception(AdiantiCoreTranslator::translate('The parameter (^1) of ^2 is required', 'key', __CLASS__));
        }

        if (empty($this->key_value))
        {
            throw new Exception(AdiantiCoreTranslator::translate('The parameter (^1) of ^2 is required', 'value', __CLASS__));
        }

        $cur_conn = serialize(TTransaction::getDatabaseInfo());
        $new_conn = serialize(TConnection::getDatabaseInfo($this->database));

        $open_transaction = ($cur_conn !== $new_conn);

        if ($open_transaction)
        {
            TTransaction::openFake($this->database);
        }

        // creates repository
        $repository = new TRepository($this->model);
        if (is_null($this->criteria))
        {
            $this->criteria = new TCriteria;
        }

        $this->criteria->setProperty('order', isset($this->ordercolumn) ? $this->ordercolumn : $this->key);


        // first
        $criteriaStart = clone $this->criteria;
        $criteriaStart->add(new TFilter($this->recusive_column, 'IS', NULL));

        $collection = $repository->load($criteriaStart, FALSE);

        // add objects to the options
        if ($collection)
        {
            foreach($collection as $object)
            {
                $items = array_merge($items, $this->makeGroups($object));
            }
        }

        if ($open_transaction)
        {
            TTransaction::close();
        }

        return $items;
    }

    public static function reloadFromModel($formname, $name, $database, $model, $key, $value, $recusive_column, $ordercolumn = NULL, TCriteria $criteria = NULL, $options = [])
    {
        $field = new self($name, $database, $model, $key, $value, $recusive_column, $ordercolumn, $criteria, $options);
        $items = $field->getItemsFromModel();

        self::reload($formname, $name, $items, $options);
    }

    public function show()
    {
        $this->setItems($this->getItemsFromModel());
        parent::show();
    }

}