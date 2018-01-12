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
 * @author    Andreas Müller <andreas.mueller@netresearch.de>
 * @copyright 2018 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */

/**
 * Dhl_Versenden_Model_Observer_Labelstatus
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Andreas Müller <andreas.mueller@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Model_Observer_Labelstatus extends Dhl_Versenden_Model_Observer_AbstractObserver
{
    /**
     * Init label status
     * - event: sales_order_place_after
     *
     * @param Varien_Event_Observer $observer
     */
    public function initLabelStatus(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = $observer->getData('order');
        $shippingMethod = $order->getShippingMethod(true);
        if ($shippingMethod->getData('carrier_code') !== Dhl_Versenden_Model_Shipping_Carrier_Versenden::CODE) {
            return;
        }

        $statusCode = Dhl_Versenden_Model_Label_Status::CODE_PENDING;
        /** @var Dhl_Versenden_Model_Label_Status $status */
        $statusLabel = Mage::getModel('dhl_versenden/label_status');
        $statusLabel->setOrderId($order->getId());
        $statusLabel->setStatusCode($statusCode);

        $statusLabel->save();
    }

    /**
     * Update label status
     * - event: dhl_versenden_create_shipment_order_after
     *
     * @param Varien_Event_Observer $observer
     */
    public function setLabelCreatedStatus(Varien_Event_Observer $observer)
    {
        /** @var Mage_Shipping_Model_Shipment_Request[] $shipmentRequests */
        $shipmentRequests = $observer->getData('request_data');
        /** @var \Dhl\Versenden\Bcs\Api\Webservice\ResponseData\CreateShipment|null $result */
        $result = $observer->getData('result');

        // create orderId to sequenceNumber mapping
        $sequenceNumbers = array();
        foreach ($shipmentRequests as $sequenceNumber => $shipmentRequest) {
            $order = $shipmentRequest->getOrderShipment()->getOrder();
            $sequenceNumbers[$order->getId()] = $sequenceNumber;
        }

        // query current status
        $orderIds = array_keys($sequenceNumbers);
        $statusCollection = Mage::getResourceModel('dhl_versenden/label_status_collection');
        $statusCollection->addFieldToFilter('order_id', array('in' => $orderIds));

        // update status
        /** @var Dhl_Versenden_Model_Label_Status $labelStatus */
        foreach ($statusCollection as $labelStatus) {
            $sequenceNumber = $sequenceNumbers[$labelStatus->getOrderId()];
            $shipmentRequest = $shipmentRequests[$sequenceNumber];
            if ($result instanceof \Dhl\Versenden\Bcs\Api\Webservice\ResponseData\CreateShipment) {
                $labelResponse = $result->getCreatedItems()->getItem($sequenceNumber);
            } else {
                $labelResponse = null;
            }

            $labelStatus->setLabelCreated($shipmentRequest->getOrderShipment()->getOrder(), $labelResponse);
        }

        // persist updated label status
        $statusCollection->save();
    }

    /**
     * Update label status
     * - event: dhl_versenden_delete_shipment_order_after
     *
     * @param Varien_Event_Observer $observer
     */
    public function setLabelDeletedStatus(Varien_Event_Observer $observer)
    {
        /** @var string[] $trackNumbers */
        $trackNumbers = $observer->getData('request_data');
        /** @var \Dhl\Versenden\Bcs\Api\Webservice\ResponseData\DeleteShipment $result */
        $result = $observer->getData('result');

        // load current label status collection
        // order ids are retrieved via track collection
        $trackCollection = Mage::getResourceModel('sales/order_shipment_track_collection');
        $trackCollection->addFieldToFilter('track_number', array('in' => $trackNumbers));

        // create orderId to shipmentNumber mapping
        $shipmentNumbers = array();
        /** @var Mage_Sales_Model_Order_Shipment_Track $track */
        foreach ($trackCollection as $track) {
            $shipmentNumbers[$track->getOrderId()] = $track->getNumber();
        }

        // query current status
        $orderIds = array_keys($shipmentNumbers);
        $statusCollection = Mage::getResourceModel('dhl_versenden/label_status_collection');
        $statusCollection->addFieldToFilter('order_id', array('in' => $orderIds));

        // update status
        /** @var Dhl_Versenden_Model_Label_Status $labelStatus */
        foreach ($statusCollection as $labelStatus) {
            $shipmentNumber = $shipmentNumbers[$labelStatus->getOrderId()];
            /** @var Mage_Sales_Model_Order_Shipment_Track $track */
            $track = $trackCollection->getItemByColumnValue('track_number', $shipmentNumber);
            $deletionResponse = $result->getDeletedItems()->getItem($shipmentNumber);

            $labelStatus->setLabelDeleted($track->getShipment()->getOrder(), $deletionResponse);
        }

        // persist updated label status
        $statusCollection->save();
    }

    /**
     * join status table to grid table
     * - event: ales_order_grid_collection_load_before
     * @param Varien_Event_Observer $observer
     */
    public function addStatusToOrderGridCollection(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Resource_Order_Grid_Collection $collection */
        $collection = $observer->getData('order_grid_collection');
        if (!array_key_exists('status', $collection->getSelect()->getPart('from'))) {
            $collection->getSelect()->joinLeft(
                array('status' => $collection->getTable('dhl_versenden/label_status')),
                'main_table.entity_id = status.order_id',
                array('status_code')
            );
        }
    }


    /**
     * Add new column dhl_shipment_status to sales order grid.
     * - event: core_layout_block_create_after
     *
     * @param Varien_Event_Observer $observer
     */
    public function addColumnToGrid(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        if (!($block instanceof Mage_Adminhtml_Block_Sales_Order_Grid)) {
            return;
        }

        $columnOptions = array(
            Dhl_Versenden_Model_Label_Status::CODE_PENDING => $block->__('Pending'),
            Dhl_Versenden_Model_Label_Status::CODE_PROCESSED => $block->__('Processed'),
            Dhl_Versenden_Model_Label_Status::CODE_FAILED => $block->__('Failed')
        );

        // Add a new column right after the "Ship to Name" column
        $block->addColumnAfter(
            'status_code',
            array(
                'header'    => $block->__('DHL Label Status'),
                'index'     => 'status_code',
                'renderer'  => 'dhl_versenden/adminhtml_sales_order_grid_renderer_icon',
                'type'      => 'options',
                'options'   => $columnOptions,
                'filter_condition_callback' => array($this, 'filterStatus')
            ),
            'status'
        );
    }

    /**
     * Filter grid by DHL label status
     *
     * @param Mage_Sales_Model_Resource_Order_Grid_Collection $collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return $this
     */
    public function filterStatus(
        Mage_Sales_Model_Resource_Order_Grid_Collection $collection,
        Mage_Adminhtml_Block_Widget_Grid_Column $column
    ) {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }

        $collection->join(
            array('status' => 'dhl_versenden/label_status'),
            'main_table.entity_id = status.order_id',
            array('status_code')
        );
        $collection->addFieldToFilter('status_code', array('eq'=> $value));

        return $this;
    }
}
