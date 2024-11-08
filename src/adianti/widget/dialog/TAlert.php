<?php

namespace Mycomponent\Widget\Dialog;

class TAlert extends \Adianti\Widget\Base\TElement
{
    /**
     * Class Constructor
     * @param $type    Type of the alert (success, info, warning, danger)
     * @param $message Message to be shown
     */
    public function __construct($type, $message, $close = false)
    {
        parent::__construct('div');
        $this->{'class'} = 'talert alert alert-dismissible alert-'.$type;
        $this->{'role'}  = 'alert';

        if($close)
        {
            $button = new \Adianti\Widget\Base\TElement('button');
            $button->{'type'} = 'button';
            $button->{'class'} = 'close';
            $button->{'data-dismiss'} = 'alert';
            $button->{'aria-label'}   = 'Close';

            $span = new \Adianti\Widget\Base\TElement('span');
            $span->{'aria-hidden'} = 'true';
            $span->add('&times;');
            $button->add($span);

            parent::add($button);
        }

        parent::add($message);
    }
}
