<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Model_Adminhtml_System_Config_Source_Yesoptno
{
    const N   = '0';
    const Y   = '1';
    const OPT = '2';

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = $this->toArray();
        $optionsArray = array();
        foreach (array(self::Y, self::OPT, self::N) as $optionValue) {
            $optionsArray[] = array('value' => $optionValue, 'label' => $options[$optionValue]);
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
        return array(
            self::N => Mage::helper('adminhtml')->__('Disable'),
            self::Y => Mage::helper('adminhtml')->__('Enable'),
            self::OPT => Mage::helper('adminhtml')->__('Enable on customers choice'),
        );
    }
}
