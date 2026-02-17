<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Block_Adminhtml_Form_Field_Procedure_Select extends Mage_Adminhtml_Block_Html_Select
{
    protected function _construct()
    {
        $this
            ->setClass('select')
            ->setTitle($this->__('Select Procedure'));
    }

    public function setInputName($value)
    {
        return $this->setName($value);
    }
}
