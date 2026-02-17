<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Model_Label_Status extends Mage_Core_Model_Abstract
{
    public const FIELD_ORDER_ID = 'order_id';
    public const FIELD_STATUS_CODE = 'status_code';

    /**
     * Other carrier, not a DHL shipment
     */
    public const CODE_OTHER = 0;

    /**
     * Label not yet requested
     */
    public const CODE_PENDING = 10;

    /**
     * Labels retrieved, all items shipped
     */
    public const CODE_PROCESSED = 20;

    /**
     * Label request failed
     */
    public const CODE_FAILED = 30;

    /**
     * object initialization
     */
    public function _construct()
    {
        $this->_init('dhl_versenden/label_status');
        parent::_construct();
    }

    /**
     * @return int
     */
    public function getOrderId()
    {
        return $this->getData(self::FIELD_ORDER_ID);
    }

    /**
     * @param int $orderId
     * @return void
     */
    public function setOrderId($orderId)
    {
        $this->setData(self::FIELD_ORDER_ID, $orderId);
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->getData(self::FIELD_STATUS_CODE);
    }

    /**
     * @param int $statusCode
     * @return void
     */
    public function setStatusCode($statusCode)
    {
        $this->setData(self::FIELD_STATUS_CODE, $statusCode);
    }

    /**
     * Update status after label creation attempt (REST API).
     *
     * @param Mage_Sales_Model_Order $order
     * @param object|null $createdItem REST SDK shipment object from successful creation
     * @return void
     */
    public function setLabelCreated(
        Mage_Sales_Model_Order $order,
        $createdItem = null
    ) {
        $this->setOrderId($order->getId());

        // Simple logic: object = success, null = failed
        if ($createdItem && is_object($createdItem)) {
            $this->setStatusCode(self::CODE_PROCESSED);
        } else {
            $this->setStatusCode(self::CODE_FAILED);
        }
    }

    /**
     * Update status after label deletion attempt.
     *
     * @param Mage_Sales_Model_Order $order
     * @param mixed $deletedItem
     * @return void
     */
    public function setLabelDeleted(
        Mage_Sales_Model_Order $order,
        $deletedItem
    ) {
        $this->setOrderId($order->getId());

        if ($deletedItem->isError()) {
            return;
        }

        $this->setStatusCode(self::CODE_PENDING);
    }
}
