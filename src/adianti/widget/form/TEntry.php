<?php
namespace Mycomponent\Widget\Form;

use Exception;
use Adianti\Control\TAction;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Base\TElement;
use Adianti\Core\AdiantiCoreTranslator;

class TEntry extends \Adianti\Widget\Form\TEntry
{
    protected $enterAction;
    function setEnterAction(TAction $action)
    {
        if ($action->isStatic())
        {
            $this->enterAction = $action;
        }
        else
        {
            $string_action = $action->toString();
            throw new Exception(AdiantiCoreTranslator::translate('Action (^1) must be static to be used in ^2', $string_action, __METHOD__));
        }
    }

    public function show()
    {
        // define the tag properties
        $this->tag->{'name'}  = $this->name;    // TAG name
        $this->tag->{'value'} = htmlspecialchars( (string) $this->value, ENT_QUOTES | ENT_HTML5, 'UTF-8');   // TAG value

        if (!empty($this->size))
        {
            if (strstr((string) $this->size, '%') !== FALSE)
            {
                $this->setProperty('style', "width:{$this->size};", false); //aggregate style info
            }
            else
            {
                $this->setProperty('style', "width:{$this->size}px;", false); //aggregate style info
            }
        }

        if ($this->id and empty($this->tag->{'id'}))
        {
            $this->tag->{'id'} = $this->id;
        }

        if (isset($this->enterAction))
        {
            if (!TForm::getFormByName($this->formName) instanceof TForm)
            {
                throw new Exception(AdiantiCoreTranslator::translate('You must pass the ^1 (^2) as a parameter to ^3', __CLASS__, $this->name, 'TForm::setFields()') );
            }
            $string_action = $this->enterAction->serialize(FALSE);
            $this->setProperty('enterAction', "__adianti_post_lookup('{$this->formName}', '{$string_action}', '{$this->id}', 'callback')");
        }

        if (isset($this->exitAction))
        {
            if (!TForm::getFormByName($this->formName) instanceof TForm)
            {
                throw new Exception(AdiantiCoreTranslator::translate('You must pass the ^1 (^2) as a parameter to ^3', __CLASS__, $this->name, 'TForm::setFields()') );
            }
            $string_action = $this->exitAction->serialize(FALSE);
            $this->setProperty('exitaction', "__adianti_post_lookup('{$this->formName}', '{$string_action}', '{$this->id}', 'callback')");
        }

        if (isset($this->exitAction))
        {
            // just aggregate onBlur, if the previous one does not have return clause
            if (strstr((string) $this->getProperty('onBlur'), 'return') == FALSE)
            {
                $this->setProperty('onBlur', $this->getProperty('exitaction'), FALSE);
            }
            else
            {
                $this->setProperty('onBlur', $this->getProperty('exitaction'), TRUE);
            }
        }

        if (isset($this->enterAction))
        {
            // just aggregate onBlur, if the previous one does not have return clause
            if (strstr((string) $this->getProperty('onBlur'), 'return') == FALSE)
            {
                $this->setProperty('onFocus', $this->getProperty('enterAction'), FALSE);
            }
            else
            {
                $this->setProperty('onFocus', $this->getProperty('enterAction'), TRUE);
            }
        }

        if (isset($this->exitFunction))
        {
            if (strstr((string) $this->getProperty('onBlur'), 'return') == FALSE)
            {
                $this->setProperty('onBlur', $this->exitFunction, FALSE);
            }
            else
            {
                $this->setProperty('onBlur', $this->exitFunction, TRUE);
            }
        }

        if ($this->mask)
        {
            TScript::create( "tentry_new_mask( '{$this->id}', '{$this->mask}'); ");
        }

        if($this->toggleVisibility)
        {
            $this->{'type'} = 'password';
            TScript::create(" tentry_toggle_visibility( '{$this->id}' ); ");
        }

        if (!empty($this->innerIcon))
        {
            $icon_wrapper = new TElement('div');
            $icon_wrapper->{'class'} = 'inner-icon-container';
            $icon_wrapper->{'id'} = "{$this->id}-container";
            $icon_wrapper->add($this->tag);
            $icon_wrapper->add($this->innerIcon);
            $icon_wrapper->show();
        }
        else
        {
            // shows the tag
            $this->tag->show();
        }

        if (isset($this->completion))
        {
            $options = [ 'minChars' => $this->minLength ];
            if (!empty($this->delimiter))
            {
                $options[ 'delimiter'] = $this->delimiter;
            }
            $options_json = json_encode( $options );
            $list = json_encode($this->completion);
            TScript::create(" tentry_autocomplete( '{$this->id}', $list, '{$options_json}'); ");
        }

        if ($this->numericMask)
        {
            $reverse = $this->reverse ? 'true' : 'false';
            $allowNegative = $this->allowNegative ? 'true' : 'false';

            TScript::create( "tentry_numeric_mask( '{$this->id}', {$this->decimals}, '{$this->decimalsSeparator}', '{$this->thousandSeparator}', {$reverse}, {$allowNegative}); ");
        }

        if ($this->exitOnEnterOn)
        {
            TScript::create( "tentry_exit_on_enter( '{$this->id}' ); ");
        }

        // verify if the widget is non-editable
        if (!parent::getEditable())
        {
            parent::disableField($this->formName, $this->name);
        }
    }
}