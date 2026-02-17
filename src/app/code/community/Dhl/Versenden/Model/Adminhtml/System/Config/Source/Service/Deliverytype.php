<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Model_Adminhtml_System_Config_Source_Service_Deliverytype
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
                'value' => \Dhl\Versenden\ParcelDe\Service\DeliveryType::ECONOMY,
                'label' => Mage::helper('dhl_versenden/data')->__('Economy'),
            ],
            [
                'value' => \Dhl\Versenden\ParcelDe\Service\DeliveryType::PREMIUM,
                'label' => Mage::helper('dhl_versenden/data')->__('Premium'),
            ],
            [
                'value' => \Dhl\Versenden\ParcelDe\Service\DeliveryType::CDP,
                'label' => Mage::helper('dhl_versenden/data')->__('Closest Drop Point'),
            ],
        ];
    }
}
