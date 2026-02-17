<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Model_Adminhtml_System_Config_Source_Shipping_Products
{
    /**
     * @return string[]
     */
    public function toOptionArray()
    {
        $optionArray = [];
        $options = Mage::getSingleton('dhl_versenden/shipping_carrier_versenden')->getProducts('DE', 'DE');

        foreach ($options as $value => $label) {
            $optionArray[] = ['value' => $value, 'label' => $label];
        }

        return $optionArray;
    }
}
