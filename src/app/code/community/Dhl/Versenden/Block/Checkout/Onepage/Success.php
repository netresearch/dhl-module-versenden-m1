<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Block_Checkout_Onepage_Success extends Mage_Checkout_Block_Success
{
    public $services = array(
        'preferred_day',
        'preferred_time',
        'preferred_location',
        'preferred_neighbour'
    );

    /**
     * Set the template for tracking.
     */
    public function _construct()
    {
        $this->setTemplate('dhl_versenden/checkout/success-tracking.phtml');
    }

    /**
     * @return \Dhl\Versenden\Bcs\Api\Info
     */
    public function getDhlVersendenInfo()
    {
        $checkoutSession = Mage::getSingleton('checkout/session');
        $orderId  = $checkoutSession->getData('last_real_order_id');
        /** @var Mage_Sales_Model_Order $orderModel */
        $orderModel = Mage::getModel('sales/order');
        $order = $orderModel->loadByIncrementId($orderId);
        $shippingAddress = $order->getShippingAddress();

        if (!$shippingAddress) {
            return null;
        }

        return $shippingAddress->getData('dhl_versenden_info');
    }

    /**
     * @return bool
     */
    public function canAddTracking()
    {
        if ($this->isServiceAvailable()) {
            /** @var Dhl_Versenden_Model_Tracking $tracking */
            $tracking = Mage::getModel('dhl_versenden/tracking');

            return $tracking->canExecute();
        }

        return false;
    }

    /**
     * @return bool
     */
    protected function isServiceAvailable()
    {
        $serviceInfo = $this->getDhlVersendenInfo();
        if (!$serviceInfo) {
            return false;
        }

        $services = $serviceInfo->getServices()->toArray();
        $result = array_intersect_key($services, array_flip($this->services));

        return  count(array_filter($result)) > 0;
    }
}
