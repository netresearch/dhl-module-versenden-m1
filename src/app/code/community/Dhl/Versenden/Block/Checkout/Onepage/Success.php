<?php
/**
 * ${PACKAGE}
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
 * @copyright Copyright (c) 2018 Netresearch GmbH & Co. KG (http://www.netresearch.de/)
 * @license   Open Software License (OSL 3.0)
 * @link      http://opensource.org/licenses/osl-3.0.php
 */

class Dhl_Versenden_Block_Checkout_Onepage_Success extends Mage_Core_Block_Template
{
    protected $services = array(
        \Dhl\Versenden\Bcs\Api\Shipment\Service\PreferredDay::CODE,
        \Dhl\Versenden\Bcs\Api\Shipment\Service\PreferredTime::CODE,
        \Dhl\Versenden\Bcs\Api\Shipment\Service\PreferredLocation::CODE,
        \Dhl\Versenden\Bcs\Api\Shipment\Service\PreferredNeighbour::CODE
    );

    public function _construct()
    {
        $this->setTemplate('dhl_versenden/checkout/success-tracking.phtml');
    }

    /**
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        $orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
        return Mage::getModel('sales/order')->loadByIncrementId($orderId);
    }

    public function isDevelopMode()
    {
        return Mage::getIsDeveloperMode();
    }

    /**
     * @return \Dhl\Versenden\Bcs\Api\Info\Services
     */
    public function getServices()
    {
        $order = $this->getOrder();
        return $order->getShippingAddress()->getData('dhl_versenden_info')->getServices();
    }

    /**
     * @return bool
     */
    public function isServiceSelected()
    {
        $services = $this->getServices();
        if ($services) {
            $serviceList = $this->services;
            $count = 0;

            foreach ($serviceList as $item) {
                $val = $services->$item;
                if ($val != null) {
                    $count++;
                }
            }
        }

        return $count > 0;
    }

    /**
     * @return bool
     */
    public function canAddTracking()
    {
        $isTrackingEnabled = Mage::getStoreConfigFlag(
            Dhl_Versenden_Model_Config::CONFIG_XML_PATH_CHECKOUT_TRACKING_ENBLED
        );

        return !Mage::getIsDeveloperMode() && $this->isServiceSelected() && $isTrackingEnabled;
    }


}
