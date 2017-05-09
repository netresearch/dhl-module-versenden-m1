<?php
/**
 * Dhl Versenden
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer versions in the future.
 *
 * PHP version 5
 *
 * @category  Dhl
 * @package   Dhl_Versenden
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */

/**
 * Dhl_Versenden_Model_Resource_Autocreate_Collection
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Model_Resource_Autocreate_Collection
    extends Mage_Sales_Model_Resource_Order_Collection
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
            array('like' => Dhl_Versenden_Model_Shipping_Carrier_Versenden::CODE . '%')
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
            array('shipment' => $this->getTable('sales/shipment')),
            'main_table.entity_id = shipment.order_id',
            array('shipment.order_id')
        );
        $this->addFieldToFilter('shipment.entity_id', array('null' => true));

        return $this;
    }

    /**
     * @param string[] $countries List of country codes
     * @return $this
     */
    public function addDeliveryCountriesFilter(array $countries)
    {
        $this->_addAddressFields();
        $this->addFieldToFilter('shipping_o_a.country_id', array('in' => $countries));

        return $this;
    }

    /**
     * @param string[] $status
     * @return $this
     */
    public function addStatusFilter(array $status)
    {
        $this->addFieldToFilter('status', array('in' => $status));

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
            $stores
        );

        $this->addFieldToFilter('main_table.store_id', array('in' => $storeIds));

        return $this;
    }
}
