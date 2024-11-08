<?php
namespace Mycomponent\Widget\Form;

use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Database\TConnection;
use Adianti\Database\TCriteria;
use Adianti\Database\TRepository;
use Adianti\Database\TTransaction;
use Mad\Widget\Form\BTreeView;

class BDBTreeView extends BTreeView
{
    private $model;
    private $key;
    private $key_value;
    private $groups;
    private $ordercolumn;
    private $criteria;

    public function __construct($name, $database, $model, $key, $key_value, $groups = [], $ordercolumn = NULL, TCriteria $criteria = NULL)
    {
        parent::__construct($name);

        $this->database = $database;
        $this->model = $model;
        $this->key = $key;
        $this->key_value = $key_value;
        $this->groups = $groups ?? [];
        $this->criteria = $criteria;
        $this->ordercolumn = $ordercolumn;
    }

    public function addGroup($groupKey, $groupValue)
    {
        $this->groups[] = [$groupKey => $groupValue];
    }

    public function setGroups($groups)
    {
        $this->groups = $groups;
    }

    public function getGroups()
    {
        return $this->groups;
    }

    private function makeGroups($items, $object, $groups)
    {
        $key = (isset($object->{$this->key})) ? $object->{$this->key} : $object->render($this->key);
        $value = (isset($object->{$this->key_value})) ? $object->{$this->key_value} : $object->render($this->key_value);

        $arrayItem = $this->recursiveGroup($object, [], $groups, $key, $value);

        return array_replace_recursive($items, $arrayItem);
    }


    public function recursiveGroup($object, $arrayItem, $groups, $k, $v)
    {
        if (empty($groups))
        {
            $arrayItem["btreekey_{$k}"] = [$v, $object];
            return $arrayItem;
        }

        $groupkey = key($groups);
        $groupvalue = $groups[$groupkey];
        unset($groups[$groupkey]);

        $gk = (isset($object->{$groupkey})) ? $object->{$groupkey} : $object->render($groupkey);
        $gv = (isset($object->{$groupvalue})) ? $object->{$groupvalue} : $object->render($groupvalue);

        $arrayItem['btreekey_' . $gk ] = [
            'label' => $gv,
            'object' => $object,
            'items' => $this->recursiveGroup($object, $arrayItem, $groups, $k, $v)
        ];

        return $arrayItem;
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

        // load all objects
        $collection = $repository->load($this->criteria, FALSE);

        // add objects to the options
        if ($collection)
        {
            foreach ($collection as $object)
            {
                $items = $this->makeGroups($items, $object, $this->groups);
            }

            self::sort($items);
        }

        if ($open_transaction)
        {
            TTransaction::close();
        }

        return $items;
    }

    public static function reloadFromModel($formname, $name, $database, $model, $key, $key_value, $groups = [], $ordercolumn = NULL, TCriteria $criteria = NULL, $options = [])
    {
        $field = new self($name, $database, $model, $key, $key_value, $groups, $ordercolumn, $criteria, $options);
        $items = $field->getItemsFromModel();

        self::reload($formname, $name, $items, $options);
    }

    public function show()
    {
        $this->setItems($this->getItemsFromModel());

        parent::show();
    }
}