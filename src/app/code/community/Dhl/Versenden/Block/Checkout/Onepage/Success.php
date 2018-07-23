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
 * Dhl_Versenden_Block_Checkout_Onepage_Success
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Andreas Müller <andreas.mueller@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Block_Checkout_Onepage_Success extends Mage_Core_Block_Template
{
    protected $services = array(
        'preferred_day',
        'preferred_time',
        'preferred_location',
        'preferred_neighbour'
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

    /**
     * @return bool
     */
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
        $order = $this->getOrder();
        $servicesinfo = $order->getShippingAddress()->getData('dhl_versenden_info');
        if (!$servicesinfo) {
            return false;
        }

        $services = $servicesinfo->getServices();
        $result = array_intersect_key($services, array_flip($this->services));

        return  count(array_filter($result)) > 0;
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
