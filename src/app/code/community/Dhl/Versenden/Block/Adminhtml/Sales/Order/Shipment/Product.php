<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Product extends Mage_Adminhtml_Block_Sales_Order_Shipment_Packaging
{
    /**
     * Get available shipping products filtered by route (shipper/recipient country).
     *
     * @return string[]
     */
    public function getProducts()
    {
        $storeId = $this->getShipment()->getStoreId();

        return Mage::getSingleton('dhl_versenden/shipping_carrier_versenden')->getProducts(
            Mage::getSingleton('dhl_versenden/config')->getShipperCountry($storeId),
            $this->getShipment()->getShippingAddress()->getCountryId()
        );
    }
}
