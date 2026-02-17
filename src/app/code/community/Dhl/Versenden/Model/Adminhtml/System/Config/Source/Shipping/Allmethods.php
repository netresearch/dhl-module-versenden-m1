<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Model_Adminhtml_System_Config_Source_Shipping_Allmethods extends Mage_Adminhtml_Model_System_Config_Source_Shipping_Allmethods
{
    /**
     * The core source model changes the meaning of the incoming parameter.
     *
     * @see Mage_Adminhtml_Block_System_Config_Form::initFields()
     * @see Mage_Adminhtml_Model_System_Config_Source_Shipping_Allmethods::toOptionArray()
     * @param bool $isMultiSelect
     * @return mixed[]
     */
    public function toOptionArray($isMultiSelect = false)
    {
        $showActiveOnlyFlag = $isMultiSelect;
        return parent::toOptionArray($showActiveOnlyFlag);
    }
}
