<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Model_Adminhtml_System_Config_Source_DropOffDays
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $days = [
            '1' => Mage::helper('dhl_versenden/data')->__('Mon'),
            '2' => Mage::helper('dhl_versenden/data')->__('Tue'),
            '3' => Mage::helper('dhl_versenden/data')->__('Wed'),
            '4' => Mage::helper('dhl_versenden/data')->__('Thu'),
            '5' => Mage::helper('dhl_versenden/data')->__('Fri'),
            '6' => Mage::helper('dhl_versenden/data')->__('Sat'),
        ];

        return $days;
    }
}
