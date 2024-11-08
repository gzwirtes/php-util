<?php
namespace Mycomponent\Widget\Form;

use Adianti\Core\AdiantiApplicationConfig;
use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Form\TField;
use Adianti\Widget\Form\THidden;

use Adianti\Control\TAction;
use Adianti\Core\AdiantiCoreApplication;
use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Service\AdiantiUploaderService;
use Exception;

/**
 * FileChooser widget
 *
 * @version    7.6
 * @package    widget
 * @subpackage form
 * @author     Nataniel Rabaioli
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 */
class TFile extends \Adianti\Widget\Form\TFile
{
    protected $download = true;
    protected $icon = 'fa-download';

    public function setDisplayMode($mode, $download = true)
    {
        $this->displayMode = $mode;
        $this->download    = $download;
    }
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    public function show()
    {
        // define the tag properties
        $this->tag->{'id'}       = $this->id;
        $this->tag->{'name'}     = 'file_' . $this->name;  // tag name
        $this->tag->{'receiver'} = $this->name;  // tag name
        $this->tag->{'value'}    = $this->value; // tag value
        $this->tag->{'type'}     = 'file';       // input type

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

        if (!empty($this->height))
        {
            $this->setProperty('style', "height:{$this->height}px;", false); //aggregate style info
        }

        $hdFileName = new THidden($this->name);
        $hdFileName->setValue( $this->value );

        $complete_action = "'undefined'";
        $error_action = "'undefined'";

        // verify if the widget is editable

        if (isset($this->completeAction) || isset($this->errorAction))
        {
            if (!\TForm::getFormByName($this->formName) instanceof \TForm)
            {
                throw new Exception(AdiantiCoreTranslator::translate('You must pass the ^1 (^2) as a parameter to ^3', __CLASS__, $this->name, 'TForm::setFields()') );
            }
        }

        if (isset($this->completeAction))
        {
            $string_action = $this->completeAction->serialize(FALSE);
            $complete_action = "function() { __adianti_post_lookup('{$this->formName}', '{$string_action}', '{$this->id}', 'callback'); tfile_update_download_link('{$this->name}') }";
        }

        if (isset($this->errorAction))
        {
            $string_action = $this->errorAction->serialize(FALSE);
            $error_action = "function() { __adianti_post_lookup('{$this->formName}', '{$string_action}', '{$this->id}', 'callback'); }";
        }

        $div = new TElement('div');
        $div->{'style'} = "display:inline;width:100%;";
        $div->{'id'} = 'div_file_'.mt_rand(1000000000, 1999999999);
        $div->{'class'} = 'div_file';

        $div->add( $hdFileName );
        if ($this->placeHolder)
        {
            $div->add( $this->tag );
            $div->add( $this->placeHolder );
            $this->tag->{'style'} = 'display:none';
        }
        else
        {
            $div->add( $this->tag );
        }

        if ($this->displayMode == 'file' AND file_exists($this->value))
        {
            $icon = TElement::tag('i', null, ['class' => "fa {$this->icon}"]);
            $link = new TElement('a');
            $link->{'id'}     = 'view_'.$this->name;
            if($this->download)
            {
                $link->{'href'}   = 'download.php?file='.$this->value;
                $link->{'target'} = 'download';
            }
            $link->{'style'}  = 'padding: 4px; display: block';
            $link->add($icon);
            $teste = explode('/',$this->value);
            $link->add(end($teste));
            $div->add( $link );
        }

        $div->show();

        if (empty($this->extensions))
        {
            $action = "engine.php?class={$this->uploaderClass}";
        }
        else
        {
            $hash = md5("{$this->seed}{$this->name}".base64_encode(serialize($this->extensions)));
            $action = "engine.php?class={$this->uploaderClass}&name={$this->name}&hash={$hash}&extensions=".base64_encode(serialize($this->extensions));
        }

        if ($router = AdiantiCoreApplication::getRouter())
        {
	        $action = $router($action, false);
        }

        $fileHandling = $this->fileHandling ? '1' : '0';
        $imageGallery = json_encode(['enabled'=> $this->imageGallery ? '1' : '0', 'width' => $this->galleryWidth, 'height' => $this->galleryHeight]);
        $popover = json_encode(['enabled' => $this->popover ? '1' : '0', 'title' => $this->poptitle, 'content' => base64_encode($this->popcontent)]);
        $limitSize = $this->limitSize ?? 'null';

        TScript::create(" tfile_start( '{$this->tag-> id}', '{$div-> id}', '{$action}', {$complete_action}, {$error_action}, $fileHandling, '$imageGallery', '$popover', {$limitSize});");

        if (!parent::getEditable())
        {
            TScript::create("tfile_disable_field('{$this->formName}', '{$this->name}');");
        }
    }
}
