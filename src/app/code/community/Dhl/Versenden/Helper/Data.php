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
 * Dhl_Versenden_Helper_Data
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Get the currently installed Dhl_Versenden version.
     *
     * @return string
     */
    public function getModuleVersion()
    {
        $moduleName = $this->_getModuleName();
        return (string)Mage::getConfig()->getModuleConfig($moduleName)->version;
    }

    /**
     * split street into street name, number and care of
     *
     * @param string $street
     *
     * @return array
     */
    public function splitStreet($street)
    {
        /*
         * first pattern  | street_name             | required | ([^0-9]+)         | all characters != 0-9
         * second pattern | additional street value | optional | ([0-9]+[ ])*      | numbers + white spaces
         * ignore         |                         |          | [ \t]*            | white spaces and tabs
         * second pattern | street_number           | optional | ([0-9]+[-\w^.]+)? | numbers + any word character
         * ignore         |                         |          | [, \t]*           | comma, white spaces and tabs
         * third pattern  | supplement              | optional | ([^0-9]+.*)?      | all characters != 0-9 + any character except newline
         */
        if (preg_match("/^([^0-9]+)([0-9]+[ ])*[ \t]*([0-9]*[-\w^.]*)?[, \t]*([^0-9]+.*)?\$/", $street, $matches)) {

            //check if street has additional value and add it to streetname
            if (preg_match("/^([0-9]+)?\$/", trim($matches[2]))) {
                $matches[1] = $matches[1] . $matches[2];

            }
            return array(
                'street_name'   => trim($matches[1]),
                'street_number' => isset($matches[3]) ? $matches[3] : '',
                'supplement'    => isset($matches[4]) ? trim($matches[4]) : ''
            );
        }
        return array(
            'street_name'   => $street,
            'street_number' => '',
            'supplement'    => ''
        );
    }

    /**
     * Convert a timestamp to a CE(S)T time string.
     *
     * @param string $timestamp The timestamp to convert
     * @param string $format The output format
     * @return string
     */
    public function utcToCet($timestamp = null, $format = 'Y-m-d H:i:s')
    {
        if (null === $timestamp) {
            $timestamp = time();
        }

        $date = new DateTime("@$timestamp");
        $timezoneCet = new DateTimeZone('Europe/Berlin');

        $intervalSpec = sprintf("PT%dS", $timezoneCet->getOffset($date));
        $date->add(new DateInterval($intervalSpec));

        return $date->format($format);
    }

    /**
     * Get template name for packaging popup.
     *
     * @param string $template dhl template
     * @param string $block block name
     * @return string
     */
    public function getPackagingPopupTemplate($template, $block)
    {
        /** @var Mage_Adminhtml_Block_Sales_Order_Shipment_Packaging $blockObject */
        $blockObject = Mage::getSingleton('core/layout')->getBlock($block);
        $shippingMethod = $blockObject->getShipment()->getOrder()->getShippingMethod(true);

        if ($shippingMethod->getData('carrier_code') !== Dhl_Versenden_Model_Shipping_Carrier_Versenden::CODE) {
            // different carrier, return standard template
            return $blockObject->getTemplate();
        }

        return $template;
    }
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
