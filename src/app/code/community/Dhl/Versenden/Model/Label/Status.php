<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Model_Label_Status extends Mage_Core_Model_Abstract
{
    const FIELD_ORDER_ID = 'order_id';
    const FIELD_STATUS_CODE = 'status_code';

    /**
     * Other carrier, not a DHL shipment
     */
    const CODE_OTHER = 0;

    /**
     * Label not yet requested
     */
    const CODE_PENDING = 10;

    /**
     * Labels retrieved, all items shipped
     */
    const CODE_PROCESSED = 20;

    /**
     * Label request failed
     */
    const CODE_FAILED = 30;

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
     * Update status after label creation attempt.
     *
     * @param Mage_Sales_Model_Order $order
     * @param \Dhl\Versenden\Bcs\Api\Webservice\ResponseData\CreateShipment\Label|null $createdItem
     * @return void
     */
    public function setLabelCreated(
        Mage_Sales_Model_Order $order,
        \Dhl\Versenden\Bcs\Api\Webservice\ResponseData\CreateShipment\Label $createdItem = null
    ) {
        $this->setOrderId($order->getId());

        if (!$createdItem) {
            // no request sent, nothing created
            $this->setStatusCode(self::CODE_FAILED);
        } elseif ($createdItem->getStatus()->isError()) {
            // request sent, label creation returned errors
            $this->setStatusCode(self::CODE_FAILED);
        } else {
            $this->setStatusCode(self::CODE_PROCESSED);
        }
    }

    /**
     * Update status after label deletion attempt.
     *
     * @param Mage_Sales_Model_Order $order
     * @param \Dhl\Versenden\Bcs\Api\Webservice\ResponseData\Status\Item $deletedItem
     * @return void
     */
    public function setLabelDeleted(
        Mage_Sales_Model_Order $order,
        \Dhl\Versenden\Bcs\Api\Webservice\ResponseData\Status\Item $deletedItem
    ) {
        $this->setOrderId($order->getId());

        if ($deletedItem->isError()) {
            return;
        }

        $this->setStatusCode(self::CODE_PENDING);
    }
}
