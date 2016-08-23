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
use \Dhl\Versenden\Webservice;
use \Dhl\Versenden\Webservice\RequestData\ShipmentOrder;
use \Dhl\Bcs\Api as VersendenApi;
/**
 * Dhl_Versenden_Helper_Webservice
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Helper_Webservice extends Dhl_Versenden_Helper_Data
{
    /**
     * Check if the given address implies delivery to a postal facility.
     *
     * @param Mage_Sales_Model_Quote_Address|Mage_Sales_Model_Order_Address $address
     * @return bool
     */
    public function isPostalFacility(Mage_Customer_Model_Address_Abstract $address)
    {
        // let 3rd party extensions add postal facility data
        $facility = new Varien_Object();

        Mage::dispatchEvent(
            'dhl_versenden_set_postal_facility', array(
                'quote_address'   => $address,
                'postal_facility' => $facility,
            )
        );

        return ($facility->getData('shop_type') !== null);
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @param string $message
     * @param string $messageType
     */
    public function addStatusHistoryComment(Mage_Sales_Model_Order $order, $message, $messageType)
    {
        // TODO(nr): use psr log types
        // TODO(nr): add dhl message type indicator, i.e. some icon
        if ($messageType === Zend_Log::ERR) {
            $message = sprintf('%s %s', '(x)', $message);
        } else {
            $message = sprintf('%s %s', '(i)', $message);
        }

        $history = Mage::getModel('sales/order_status_history')
            ->setOrder($order)
            ->setStatus($order->getStatus())
            ->setComment($message)
            ->setData('entity_name', Mage_Sales_Model_Order::HISTORY_ENTITY_NAME);

        $historyCollection = $order->getStatusHistoryCollection();
        $historyCollection->addItem($history);
        $historyCollection->save();
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @param string $message
     */
    public function addStatusHistoryError(Mage_Sales_Model_Order $order, $message)
    {
        $this->addStatusHistoryComment($order, $message, Zend_Log::ERR);
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @param string $message
     */
    public function addStatusHistoryInfo(Mage_Sales_Model_Order $order, $message)
    {
        $this->addStatusHistoryComment($order, $message, Zend_Log::INFO);
    }
}
