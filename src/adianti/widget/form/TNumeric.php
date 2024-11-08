<?php
namespace Mycomponent\Widget\Form;
class TNumeric extends \Mycomponent\Widget\Form\TEntry
{
    public function __construct($name, $decimals, $decimalsSeparator, $thousandSeparator, $replaceOnPost = true, $reverse = FALSE, $allowNegative = TRUE)
    {
        parent::__construct($name);
        parent::setNumericMask($decimals, $decimalsSeparator, $thousandSeparator, $replaceOnPost, $reverse, $allowNegative);
    }

     /**
     * Define input allow negative
     */
    public function setAllowNegative($allowNegative)
    {
        $this->allowNegative = $allowNegative;
    }
}