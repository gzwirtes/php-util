<?php

namespace GX4\Trait;

use Adianti\Registry\TSession;
use Adianti\Control\TPage;
use Adianti\Widget\Form\TButton;
use Adianti\Widget\Dialog\TMessage;

trait ListTrait
{
    public function onRefresh($param = null)
    {
        $this->onReload([]);
    }

    public function onClearFilters($param = null)
    {
        TSession::setValue(__CLASS__.'_filter_data', NULL);
        TSession::setValue(__CLASS__.'_filters', NULL);

        $this->onReload(['offset' => 0, 'first_page' => 1]);
    }

    public static function onShowCurtainFilters($param = null)
    {
        try
        {
            $filter = new self([]);

            $btnClose = new TButton('closeCurtain');
            $btnClose->class = 'btn btn-sm btn-default';
            $btnClose->style = 'margin-right:10px;';
            $btnClose->onClick = "Template.closeRightPanel();";
            $btnClose->setLabel("Fechar");
            $btnClose->setImage('fas:times');

            $filter->form->addHeaderWidget($btnClose);

            $page = new TPage();
            $page->setTargetContainer('adianti_right_panel');
            $page->setProperty('page-name', __CLASS__);
            $page->setProperty('page_name', __CLASS__);
            $page->adianti_target_container = 'adianti_right_panel';
            $page->target_container = 'adianti_right_panel';
            $page->add($filter->form);
            $page->setIsWrapped(true);
            $page->show();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
}