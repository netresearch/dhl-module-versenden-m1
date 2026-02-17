<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Model_Adminhtml_System_Config_Source_Service_Endorsement
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => '',
                'label' => Mage::helper('dhl_versenden/data')->__('-- Please Select --'),
            ],
            [
                'value' => \Dhl\Versenden\ParcelDe\Service\Endorsement::RETURN,
                'label' => Mage::helper('dhl_versenden/data')->__('Return'),
            ],
            [
                'value' => \Dhl\Versenden\ParcelDe\Service\Endorsement::ABANDON,
                'label' => Mage::helper('dhl_versenden/data')->__('Abandon'),
            ],
        ];
    }
}
