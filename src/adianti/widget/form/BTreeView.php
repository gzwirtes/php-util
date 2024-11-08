<?php

namespace Mycomponent\Widget\Form;

use Adianti\Control\TAction;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Widget\Form\TButton;
use Adianti\Widget\Form\TField;
use Adianti\Widget\Util\TImage;

class BTreeView extends TField implements AdiantiWidgetInterface
{
    private $items;
    private $item_action;
    private $group_action;
    private $group_transformer;
    private $item_transformer;
    private $check;
    private $expand;
    private $height;
    private $width;
    private $startOpened;
    private $container;
    private $iconOpened;
    private $iconClosed;

    public function __construct($name)
    {
        parent::__construct($name);

        $this->id = 'btreeview_' . mt_rand(1000000000, 1999999999);

        $this->tag->setName('div');
        $this->tag->id = $this->id;
        $this->tag->class = 'btreeview';
        $this->tag->btreeview = $name;
        $this->tag->widget = 'btreeview';

        $this->width = '100%';
        $this->height = '100%';
        $this->startOpened = true;
        $this->expand = true;
        $this->check = false;
        $this->container = false;

        $this->items = [];
        $this->group_action = [];
        $this->item_action = [];

        $this->iconOpened = new TImage('fa:minus');
        $this->iconClosed = new TImage('fa:plus');
    }

    public function setIcons(TImage $opened, TImage $closed)
    {
        $this->iconOpened = $opened;
        $this->iconClosed = $closed;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function enableCheck()
    {
        $this->check = true;
    }

    public function disableCheck()
    {
        $this->check = false;
    }

    public function isCheck()
    {
        return $this->check;
    }

    public function enableExpander()
    {
        $this->expand = true;
    }

    public function disableExpander()
    {
        $this->expand = false;
    }

    public function isExpand()
    {
        return $this->expand;
    }

    public function enableContainer()
    {
        $this->container = true;
    }

    public function disableContainer()
    {
        $this->container = false;
    }

    public function isContainer()
    {
        return $this->container;
    }

    public function setStartOpened($open = true)
    {
        $this->startOpened = $open;
    }

    public function setSize($width, $height = '100%')
    {
        $width = (strstr($width, '%') !== FALSE) ? $width : "{$width}px";
        $height = (strstr($height, '%') !== FALSE) ? $height : "{$height}px";

        $this->width = $width;
        $this->height = $height;
    }

    public function setGroupTransformer($callable)
    {
        $this->group_transformer = $callable;
    }

    public function setItemTransformer($callable)
    {
        $this->item_transformer = $callable;
    }

    public function getSize()
    {
        return [$this->width, $this->height];
    }

    public function addGroupAction(TAction $action, $label, $icon)
    {
        $this->group_action[] = [$action, $label, $icon];
        return $action;
    }

    public function addItemAction(TAction $action, $label, $icon)
    {
        $this->item_action[] = [$action, $label, $icon];
        return $action;
    }

    public function getItemAction()
    {
        return $this->item_action;
    }

    public function getGroupAction()
    {
        return $this->group_action;
    }

    public function clearItemAction()
    {
        $this->item_action = [];
    }

    public function clearGroupAction()
    {
        $this->group_action = [];
    }

    public function setItems($items)
    {
        $this->items = $items;
    }

    public function getItems()
    {
        return $this->items;
    }

    private function makeActions($actions, $name, $items, $object)
    {
        if (empty($actions))
        {
            return [];
        }

        $buttons = [];

        foreach($actions as $actionParameter)
        {
            list($actionDefault, $label, $image) = $actionParameter;

            $action = $object ? $actionDefault->prepare($object) : clone $actionDefault;
            $action->setParameter('key', $name);
            $action->setParameter('items', base64_encode(json_encode($items)));

            $btn = new TButton('button_btreeview_' . mt_rand(1000000000, 1999999999));
            $btn->setAction($action);
            $btn->setImage($image);
            $btn->setLabel('');
            $btn->title = $label;
            $btn->class = '';

            $btn->setFormName($this->getFormName());

            $buttons[] = $btn;
        }

        return $buttons;
    }

    private function makeTitle($key, $name, $items, $object = null)
    {
        $this->iconOpened->class .= ' btreeview-icon-opened';
        $this->iconClosed->class .= ' btreeview-icon-closed';

        $actionsGroup = $this->makeActions($this->group_action, $key, $items, $object);

        $check = '';

        if ($this->check)
        {
            $check = new TElement('input');
            $check->type = 'checkbox';
            $check->value = $key;
            $check->class = 'btreeview-checkbox-group';
            $check->onclick = 'btreeview_toggle_all(event, this)';
        }

        $name = is_array($name) ? $name[0] : $name;

        $div = new TElement('div');
        $div->class = 'btreeview-group-title';

        if ($this->group_transformer)
        {
            $name = call_user_func($this->group_transformer, $name, $key, $items, $div);
        }

        if ($this->expand)
        {
            $div->add(TElement::tag('div', [$this->iconOpened, $this->iconClosed], ['class' => 'btreeview-group-icon']));
        }

        $div->add(TElement::tag('div', [$check, $name], ['class' => 'btreeview-group-name']));
        $div->add(TElement::tag('div', $actionsGroup, ['class' => 'btreeview-group-actions']));

        return $div;
    }

    private function makeGroup($key, $name, $items, $index = 1, $object = null)
    {
        $group = new TElement('div');
        $group->class = 'btreeview-group';
        $group->id = $key;

        if ($this->startOpened)
        {
            $group->class .= ' btreeview-open';
        }

        $group_name = $this->makeTitle($key, $name, $items, $object);
        $group_items = TElement::tag('div', '', ['data-index' => $index, 'class' => 'btreeview-group-items']);

        $group->add($group_name);
        $group->add($group_items);

        foreach($items as $key => $item)
        {
            $key = str_replace('btreekey_', '', (string) $key);

            if (!empty($item['items']) && is_array($item['items']))
            {
                $group_items->add($this->makeGroup($key, $item['label'], $item['items'], ++$index, $item['object'] ?? null));
            }
            else
            {
                $group_items->last = 'true';
                $div = new TElement('div');
                $div->class = 'btreeview-item';

                if ($this->item_transformer)
                {
                    $item = call_user_func($this->item_transformer, $item, $key, $div);
                }

                $div->class = 'btreeview-item';

                $labelItem = is_array($item) ? $item[0] : $item;
                $actionsItems = is_array($item) ? $this->makeActions($this->item_action, $key, $item[0], $item[1]) : $this->makeActions($this->item_action, $key, $item, null);

                if ($this->check)
                {
                    $check = new TElement('input');
                    $check->type = 'checkbox';
                    $check->class = 'btreeview-checkbox-item';
                    $check->name = $this->name . '[]';
                    $check->value = $key;

                    if (is_array($this->value) && in_array($key, $this->value))
                    {
                        $check->checked = true;
                    }

                    $label = TElement::tag('label', [$check, TElement::tag('span', $labelItem)], ['class' => 'btreeview-item-title']);
                    $div->add($label);
                }
                else
                {
                    $div->add(TElement::tag('div', $labelItem, ['class' => 'btreeview-item-title']));
                }

                $div->add(TElement::tag('div', $actionsItems, ['class' => 'btreeview-item-actions']));

                $group_items->add($div);
            }
        }

        return $group;
    }

    public function show()
    {
        if ($this->items)
        {
            foreach ($this->items as $key => $items)
            {
                $key = str_replace('btreekey_', '', (string) $key);

                if(is_array($items) && !empty($items['label']))
                {
                    $this->tag->add($this->makeGroup($key, $items['label'], $items['items'] ?? [], 1, $items['object'] ?? NULL));
                }
                else
                {
                    $this->tag->add($this->makeGroup($key, $items, [], 1, $items['object'] ?? NULL));
                }
            }
        }

        if ($this->container)
        {
            $this->tag->class .= ' btreeview-container';
        }

        $this->tag->show();

        $expand = $this->expand ? 'true' : 'false';

        TScript::create("btreeview_start('{$this->id}', {$expand})");
    }

    public static function reload($formname, $name, $items, $options = [])
    {
        $field = new self($name);
        $field->setFormName($formname);

        if (isset($options['expand']))
        {
            $options['expand'] ? $field->enableExpander() : $field->disableExpander();
        }

        if (isset($options['check']))
        {
            $options['check'] ? $field->enableCheck() : $field->disableCheck();
        }

        if (isset($options['container']))
        {
            $options['container'] ? $field->enableContainer() : $field->disableContainer();
        }

        if (! empty($options['value']))
        {
            $field->setValue($options['value']);
        }

        if (! empty($options['item_transformer']))
        {
            $field->setItemTransformer($options['item_transformer']);
        }

        if (! empty($options['group_transformer']))
        {
            $field->setGroupTransformer($options['group_transformer']);
        }

        if (! empty($options['startOpened']))
        {
            $field->setStartOpened();
        }

        if (! empty($options['size']))
        {
            $field->setSize($options['size'][0], $options['size'][1]??null);
        }

        if (! empty($options['group_actions']))
        {
            foreach($options['group_actions'] as $action)
            {
                $field->addGroupAction($action[0], $action[1]??'', $action[2]??'');
            }
        }

        if (! empty($options['item_actions']))
        {
            foreach($options['item_actions'] as $action)
            {
                $field->addItemAction($action[0], $action[1]??'', $action[2]??'');
            }
        }

        $field->setItems($items);

        $content = base64_encode($field->getContents());

        TScript::create( " btreeview_reload('{$formname}', '{$name}', `{$content}`); " );
    }

    protected static function sort(&$array)
    {
        foreach ($array as &$value)
        {
            if (!empty($value['items']))
            {
                self::sort($value['items']);
            }
        }

        uasort($array, function($a,$b){
            if (empty($a['label'])) {
                return $a[0] <=> $b[0];
            }
            return $a['label'] <=> $b['label'];
        });
    }
}