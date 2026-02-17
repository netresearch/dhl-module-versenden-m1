<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Packaging extends Mage_Adminhtml_Block_Sales_Order_Shipment_Packaging
{
    /**
     * Obtain selected weight unit from config.
     *
     * @return string
     */
    public function getStoreUnit()
    {
        $settings = Mage::getModel('dhl_versenden/config_shipment')
            ->getSettings($this->getShipment()->getStoreId());
        return $settings->getUnitOfMeasure();
    }

    /**
     * Obtain possible weight units from carrier.
     *
     * @return string[]
     */
    public function getWeightUnits()
    {
        return Mage::getSingleton('dhl_versenden/shipping_carrier_versenden')->getCode('unit_of_measure');
    }

    /**
     * Build JSON rules for client-side service compatibility enforcement.
     *
     * @return string JSON
     */
    public function getCompatibilityRulesJson()
    {
        /** @var Dhl_Versenden_Model_Services_CompatibilityRules $rules */
        $rules = Mage::getModel(
            'dhl_versenden/services_compatibilityRules',
            ['order' => $this->getShipment()->getOrder()],
        );

        return Mage::helper('core')->jsonEncode($rules->getRules());
    }

    /**
     * Do customs information have to be added?
     *
     * @return bool
     */
    public function displayCustomsValue()
    {
        $shipperCountry = Mage::getModel('dhl_versenden/config')->getShipperCountry($this->getShipment()->getStoreId());
        $recipientCountry = $this->getShipment()->getOrder()->getShippingAddress()->getCountryId();
        $recipientPostalCode = $this->getShipment()->getShippingAddress()->getPostcode();

        return $this->helper('dhl_versenden/data')->isCollectCustomsData(
            $shipperCountry,
            $recipientCountry,
            $recipientPostalCode,
        );
    }

}
