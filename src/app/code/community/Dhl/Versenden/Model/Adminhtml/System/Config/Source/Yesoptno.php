<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Model_Adminhtml_System_Config_Source_Yesoptno
{
    public const N   = '0';
    public const Y   = '1';
    public const OPT = '2';

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = $this->toArray();
        $optionsArray = [];
        foreach ([self::Y, self::OPT, self::N] as $optionValue) {
            $optionsArray[] = ['value' => $optionValue, 'label' => $options[$optionValue]];
        }

        return $optionsArray;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            self::N => Mage::helper('adminhtml')->__('Disable'),
            self::Y => Mage::helper('adminhtml')->__('Enable'),
            self::OPT => Mage::helper('adminhtml')->__('Enable on customers choice'),
        ];
    }
}
