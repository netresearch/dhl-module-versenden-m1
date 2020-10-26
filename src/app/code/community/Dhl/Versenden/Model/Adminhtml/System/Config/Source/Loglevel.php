<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Model_Adminhtml_System_Config_Source_Loglevel
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = array();

        $options = $this->toArray();
        foreach ($options as $value => $label) {
            $optionArray[]= array('value' => $value, 'label' => $label);
        }

        return $optionArray;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            Zend_Log::ERR   => Mage::helper('dhl_versenden/data')->__('Error'),
            Zend_Log::WARN  => Mage::helper('dhl_versenden/data')->__('Warning'),
            Zend_Log::DEBUG => Mage::helper('dhl_versenden/data')->__('Debug')
        );
    }
}
