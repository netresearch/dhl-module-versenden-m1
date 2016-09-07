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
 * @author    Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 *
 */

/**
 * Dhl_Versenden_Model_Cron
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */

class Dhl_Versenden_Model_Cron
{
    /**
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function autoCreateShippment(Varien_Event_Observer $observer)
    {
        $orderCollection = Mage::helper('dhl_versenden/data')->getOrdersForAutoCreateShippment();

        /** @var Mage_Sales_Model_Order $order */
        foreach ($orderCollection as $order) {

            try {
                $shipment = $order->prepareShipment();
                $shipment->register();
                $order->setIsInProcess(true);

                Mage::helper('dhl_versenden/data')->addStatusHistoryComment(
                    $order,
                    'Automatically shipped by DHL_Versenden.',
                    Zend_Log::INFO
                );

                $transaction = Mage::getModel('core/resource_transaction');
                $transaction
                    ->addObject($shipment)
                    ->addObject($shipment->getOrder())
                    ->save();


            } catch (Exception $e) {
                Mage::log(
                    'An Error occured while creating shipment. Errormessage: ' . $e->getMessage()
                );
            }
        }

        return $this;
    }
}