<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Packaging_Grid
    extends Mage_Adminhtml_Block_Sales_Order_Shipment_Packaging_Grid
{
    /**
     * @var string[]
     */
    protected $_countriesOfManufacture = array();

    /**
     * Update template if additional customs data needs to be collected.
     */
    public function __construct()
    {
        parent::__construct();
        if ($this->displayCustomsValue()) {
            $this->setTemplate('dhl_versenden/sales/packaging_grid.phtml');
        }
    }

    /**
     * Can display customs value
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
            $recipientPostalCode
        );
    }

    /**
     * @return string[]
     */
    public function getCountries()
    {
        return Mage::getSingleton('adminhtml/system_config_source_country')->toOptionArray(true);
    }

    /**
     * Obtain the given product's country of manufacture.
     *
     * @param int $productId
     * @return string
     */
    public function getCountryOfManufacture($productId)
    {
        if (empty($this->_countriesOfManufacture)) {
            /** @var Mage_Sales_Model_Resource_Order_Shipment_Item_Collection|Mage_Sales_Model_Order_Shipment_Item[] $items */
            $items = $this->getCollection();
            if (!is_array($items)) {
                $items = $items->getItems();
            }

            $productIds = array_map(
                function (Mage_Sales_Model_Order_Shipment_Item $item) {
                    return $item->getProductId();
                }, $items
            );

            $productCollection = Mage::getResourceModel('catalog/product_collection');
            $productCollection
                ->addStoreFilter($this->getShipment()->getStoreId())
                ->addFieldToFilter('entity_id', array('in' => $productIds))
                ->addAttributeToSelect('country_of_manufacture', true);
            ;

            while ($product = $productCollection->fetchItem()) {
                $this->_countriesOfManufacture[$product->getId()] = $product->getData('country_of_manufacture');
            }
        }

        if (!isset($this->_countriesOfManufacture[$productId])) {
            // fallback to shipper country
            return Mage::getModel('dhl_versenden/config')->getShipperCountry($this->getShipment()->getStoreId());
        }

        return $this->_countriesOfManufacture[$productId];
    }
}
