<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Model_Resource_Autocreate_Collection extends Mage_Sales_Model_Resource_Order_Collection
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix    = 'dhlversenden_autocreate_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject    = 'autocreate_collection';

    /**
     * @return $this
     */
    public function addShippingMethodFilter()
    {
        // only consider orders with DHL Versenden carrier
        $this->addFieldToFilter(
            'shipping_method',
            ['like' => Dhl_Versenden_Model_Shipping_Carrier_Versenden::CODE . '%'],
        );

        return $this;
    }

    /**
     * @return $this
     */
    public function addShipmentFilter()
    {
        // only consider orders that have no shipments yet
        $this->getSelect()->joinLeft(
            ['shipment' => $this->getTable('sales/shipment')],
            'main_table.entity_id = shipment.order_id',
            ['shipment.order_id'],
        );
        $this->addFieldToFilter('shipment.entity_id', ['null' => true]);

        return $this;
    }

    /**
     * @param string[] $countries List of country codes
     * @return $this
     */
    public function addDeliveryCountriesFilter(array $countries)
    {
        $this->_addAddressFields();
        $this->addFieldToFilter('shipping_o_a.country_id', ['in' => $countries]);

        return $this;
    }

    /**
     * @param string[] $status
     * @return $this
     */
    public function addStatusFilter(array $status)
    {
        $this->addFieldToFilter('status', ['in' => $status]);

        return $this;
    }

    /**
     * @param mixed[] $stores
     * @return $this
     */
    public function addStoreFilter(array $stores)
    {
        $storeIds = array_map(
            function ($store) {
                return Mage::app()->getStore($store)->getId();
            },
            $stores,
        );

        $this->addFieldToFilter('main_table.store_id', ['in' => $storeIds]);

        return $this;
    }
}
