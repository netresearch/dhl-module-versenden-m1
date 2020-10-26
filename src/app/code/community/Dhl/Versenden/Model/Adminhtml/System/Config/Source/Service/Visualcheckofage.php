<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Model_Adminhtml_System_Config_Source_Service_Visualcheckofage
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $optionsArray = array(
            array(
            'value' => 0,
            'label' => Mage::helper('dhl_versenden/data')->__('No')
            ),
            array(
                'value' => \Dhl\Versenden\Bcs\Api\Shipment\Service\VisualCheckOfAge::A16,
                'label' => \Dhl\Versenden\Bcs\Api\Shipment\Service\VisualCheckOfAge::A16,
            ),
            array(
                'value' => \Dhl\Versenden\Bcs\Api\Shipment\Service\VisualCheckOfAge::A18,
                'label' => \Dhl\Versenden\Bcs\Api\Shipment\Service\VisualCheckOfAge::A18,
            )
        );

        return $optionsArray;
    }


}
