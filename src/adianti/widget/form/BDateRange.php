<?php
namespace Mycomponent\Widget\Form;

use Adianti\Control\TAction;
use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TField;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Util\TImage;

use DateTime;
use Exception;

class BDateRange extends TField implements AdiantiWidgetInterface
{
    protected $mask;
    protected $dbmask;
    protected $id;
    protected $size;
    protected $options;
    protected $value;
    protected $changeAction;
    protected $name_end;
    protected $title;
    protected $grid;
    protected $calendars;
    protected $autoApply;
    protected $stepSeconds;
    protected $stepHours;
    protected $stepMinutes;
    protected $separator;
    protected $valueStart;
    protected $valueEnd;
    protected $disableDates;
    protected $enableDates;
    private $parent;

    /**
     * Class Constructor
     * @param $name Name of the widget
     */
    public function __construct($name, $name_end = null)
    {
        parent::__construct($name);
        $this->id   = 'bdaterange_' . mt_rand(1000000000, 1999999999);
        $this->mask = 'DD/MM/YYYY';
        $this->dbmask = 'YYYY-MM-DD';
        $this->name = $name;
        $this->title = false;
        $this->autoApply = true;
        $this->name_end = $name_end;
        $this->grid = 2;
        $this->calendars = 2;
        $this->stepSeconds = 10;
        $this->stepHours = 1;
        $this->stepMinutes = 5;
        $this->separator = ' - ';

        if($this->name_end)
        {
            $this->separator = null;
        }

        $this->disableDates = false;
        $this->enableDates = false;
        $this->options = ['zIndex' => 10];

        $this->tag->{'widget'} = 'bdaterange';
        $this->tag->{'autocomplete'} = 'off';
    }

    public function setFormName($name)
    {
        parent::setFormName($name);

        if ($this->name_end) {
            $form = TForm::getFormByName($name);
            $end = clone $this;
            $end->setName($this->name_end);
            $end->name_end = null;
            $end->parent = $this;
            $form->addField($end);
        }
    }

    /**
     * Set enable dates
     */
    public function setEnableDates(array $dates)
    {
        $this->enableDates = $dates;
    }

    /**
     * Get disable dates
     */
    public function getDisableDates()
    {
        return $this->disableDates;
    }

    /**
     * Set disable dates
     */
    public function setDisableDates(array $dates)
    {
        $this->disableDates = $dates;
    }

    /**
     * Get enable dates
     */
    public function getEnableDates()
    {
        return $this->enableDates;
    }

    /**
     * Set separator
     */
    public function setSeparator(string $separator)
    {
        $this->separator = $separator;
    }

    /**
     * Get separator
     */
    public function getSeparator()
    {
        return $this->separator;
    }

    /**
     * Set calendars popover
     */
    public function setCalendars(int $calendars)
    {
        $this->calendars = $calendars;
    }

    /**
     * Get calendars popover
     */
    public function getCalendars()
    {
        return $this->calendars;
    }

    /**
     * Set steps
     */
    public function setSteps($stepHours = 1, $stepMinutes = 5, $stepSeconds = 10)
    {
        $this->stepHours = $stepHours;
        $this->stepMinutes = $stepMinutes;
        $this->stepSeconds = $stepSeconds;
    }

    /**
     * Set grid popover
     */
    public function setGrid(int $grid)
    {
        $this->grid = $grid;
    }

    /**
     * Set show confirm buttons
     */
    public function showConfirmButtons()
    {
        $this->autoApply = false;
    }

    /**
     * Set show confirm buttons
     */
    public function hideConfirmButtons()
    {
        $this->autoApply = true;
    }

    /**
     * Get grid popover
     */
    public function getGrid()
    {
        return $this->grid;
    }

    /**
     * Set title popover
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * Get title popover
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Store the value inside the object
     */
    public function setValue($value)
    {
        $v = $value;
        if (!empty($this->dbmask) and ($this->mask !== $this->dbmask) )
        {
            if (! empty($this->separator))
            {
                $values = explode($this->separator, $value);
                $v1 = self::replaceToMask($values[0], $this->dbmask, $this->mask);
                $v2 = self::replaceToMask($values[1], $this->dbmask, $this->mask);
                $v = $v1 . $this->separator . $v2;
            }
            else
            {
                $v = self::replaceToMask($value, $this->dbmask, $this->mask);
            }
        }

        if ($this->parent) {
            $this->parent->valueEnd = $v;
        } else {
            $this->valueStart = $v;
        }
    }

    /**
     * Return the post data
     */
    public function getPostData()
    {
        $value = $_POST[$this->name] ?? null;

        if (!empty($this->dbmask) and ($this->mask !== $this->dbmask) )
        {
            if (! empty($this->separator))
            {
                $values = explode($this->separator, $value);
                $v1 = self::replaceToMask($values[0], $this->mask, $this->dbmask);
                $v2 = self::replaceToMask($values[1], $this->mask, $this->dbmask);
                return $v1 . $this->separator . $v2;
            }

            return self::replaceToMask($value, $this->mask, $this->dbmask);
        }
        else
        {
            return $value;
        }
    }

    /**
     * Define the field's mask
     * @param $mask  Mask for the field (dd-mm-yyyy)
     */
    public function setMask($mask)
    {
        $this->mask = $mask;
    }

    /**
     * Return mask
     */
    public function getMask()
    {
        return $this->mask;
    }

    /**
     * Set the mask to be used to colect the data
     */
    public function setDatabaseMask($mask)
    {
        $this->dbmask = $mask;
    }

    /**
     * Return database mask
     */
    public function getDatabaseMask()
    {
        return $this->dbmask;
    }

    /**
     * Set extra easepick options
     * @link https://easepick.com/
     */
    public function setOption($option, $value)
    {
        $this->options[$option] = $value;
    }

    /**
     * Define the action to be executed when the user changes the field
     * @param $action TAction object
     */
    public function setExitAction(TAction $action)
    {
        $this->setChangeAction($action);
    }

    /**
     * Define the action to be executed when the user changes the field
     * @param $action TAction object
     */
    public function setChangeAction(TAction $action)
    {
        if ($action->isStatic())
        {
            $this->changeAction = $action;
        }
        else
        {
            $string_action = $action->toString();
            throw new Exception(AdiantiCoreTranslator::translate('Action (^1) must be static to be used in ^2', $string_action, __METHOD__));
        }
    }

    /**
     * Enable the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function enableField($form_name, $field)
    {
        TScript::create( " bdaterange_enable_field('{$form_name}', '{$field}'); " );
    }

    /**
     * Disable the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function disableField($form_name, $field)
    {
        TScript::create( " bdaterange_disable_field('{$form_name}', '{$field}'); " );
    }

    public static function convertMask($mask)
    {
        return str_replace(['d', 'm', 'y', 'i', 's', 'h'], ['D', 'M', 'Y', 'm', 's', 'H'], strtolower($mask));
    }

    public static function replaceToMask($value, $fromMask, $toMask)
    {
        if (empty($value))
        {
            return $value;
        }

        $value = substr($value,0,strlen($toMask));

        $phpFromMask = str_replace( ['dd','mm', 'yyyy', 'hh', 'ii', 'ss'], ['d','m','Y', 'H', 'i', 's'], strtolower($fromMask));
        $phpToMask   = str_replace( ['dd','mm', 'yyyy', 'hh', 'ii', 'ss'], ['d','m','Y', 'H', 'i', 's'], strtolower($toMask));

        $date = DateTime::createFromFormat($phpFromMask, $value);

        if ($date)
        {
            return $date->format($phpToMask);
        }

        return $value;
    }

    /**
     * Shows the widget at the screen
     */
    public function show()
    {
        $entryStart = new TEntry($this->name);
        $entryStart->setId('start_'.$this->id);
        $entryStart->setValue($this->valueStart);
        $entryStart->widget = 'bdaterange';

        if($this->mask)
        {
            $newmask = $this->mask;
            $newmask = str_ireplace('dd',   '99',   $newmask);
            $newmask = str_ireplace('mm',   '99',   $newmask);
            $newmask = str_ireplace('yyyy', '9999', $newmask);
            $entryStart->setMask($newmask);
        }

        $container = new TElement('div');
        $container->id = $this->id;
        $container->setProperties($this->properties);
        $container->class = 'bdaterange-container tdate-group date';

        $container->add($entryStart);

        $entryEnd = null;
        if ($this->name_end)
        {
            $entryEnd = new TEntry($this->name_end);
            $entryEnd->setId('end_'.$this->id);
            $entryEnd->widget = 'bdaterange';
            $entryEnd->setValue($this->valueEnd);
            $entryEnd->class .= ' bdaterange_end_field ';
            if($this->mask)
            {
                $newmask = $this->mask;
                $newmask = str_ireplace('dd',   '99',   $newmask);
                $newmask = str_ireplace('mm',   '99',   $newmask);
                $newmask = str_ireplace('yyyy', '9999', $newmask);
                $entryEnd->setMask($newmask);
            }

            $container->add($entryEnd);
            $container->class .= ' start_end';
        }
        elseif($this->separator && $this->mask)
        {
            $newmask = $this->mask;
            $newmask = str_ireplace('dd',   '99',   $newmask);
            $newmask = str_ireplace('mm',   '99',   $newmask);
            $newmask = str_ireplace('yyyy', '9999', $newmask);
            $entryStart->setMask($newmask.$this->separator.$newmask);
        }

        if (!empty($this->size))
        {
            if (strstr((string) $this->size, '%') !== FALSE)
            {
                $container->setProperty('style', "width:{$this->size};", false); //aggregate style info
                $entryStart->setProperty('style', "width:{$this->size};", false); //aggregate style info
                if($entryEnd)
                {
                    $entryEnd->setProperty('style', "width:{$this->size};", false); //aggregate style info
                }
            }
            else
            {
                $container->setProperty('style', "width:{$this->size}px;", false); //aggregate style info
                $entryStart->setProperty('style', "width:{$this->size}px;", false); //aggregate style info
                if($entryStart)
                {
                    $entryStart->setProperty('style', "width:{$this->size};", false); //aggregate style info
                }
            }
        }

        $container->add(TElement::tag('span', new TImage('far:calendar'), ['class' => 'bdaterange-icon btn btn-default tdate-group-addon']));

        if (isset($this->changeAction))
        {
            if (!TForm::getFormByName($this->formName) instanceof TForm)
            {
                throw new Exception(AdiantiCoreTranslator::translate('You must pass the ^1 (^2) as a parameter to ^3', __CLASS__, $this->name, 'TForm::setFields()') );
            }

            $string_action = $this->changeAction->serialize(FALSE);
            $this->options['changeaction'] = "__adianti_post_lookup('{$this->formName}', '{$string_action}', '{$this->id}', 'callback');";
        }

        if (! empty($this->enableDates && ! empty($this->disableDates)))
        {
            throw new Exception('Only inform enable or disable dates');
        }

        $this->options['seconds'] = strpos($this->mask, 'ss') !== false;
        $this->options['time'] = strpos(strtolower($this->mask), 'h') !== false || strpos(strtolower($this->mask), 'i') !== false;
        $this->options['stepSeconds'] = $this->stepSeconds;
        $this->options['stepHours'] = $this->stepHours;
        $this->options['stepMinutes'] = $this->stepMinutes;
        $this->options['separator'] = $this->separator;
        $this->options['format'] = self::convertMask($this->mask);
        $this->options['autoApply'] = $this->autoApply;
        $this->options['grid'] = $this->grid;
        $this->options['calendars'] = $this->calendars;
        $this->options['title'] = $this->title;
        $this->options['name_start'] = $this->name;
        $this->options['name_end'] = $this->name_end;
        $this->options['disableDates'] = $this->disableDates;
        $this->options['enableDates'] = $this->enableDates;
        $this->options['id_start'] = 'start_'.$this->id;
        $this->options['id_end'] = 'end_'.$this->id;
        $this->options['language'] = strtolower( AdiantiCoreTranslator::getLanguage() );

        $options = json_encode($this->options);

        $container->show();

        TScript::create( "bdaterange_start('{$this->id}', {$options});");

        if (!parent::getEditable())
        {
            TScript::create( " bdaterange_disable_field( '{$this->formName}', '{$this->id}' ); " );
        }
    }
}