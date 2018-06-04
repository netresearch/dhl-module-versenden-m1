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
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
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
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */

class Dhl_Versenden_Model_Cron
{
    const CRON_MESSAGE_LABELS_RETRIEVED = '%d labels were retrieved for %d orders.';
    const CRON_MESSAGE_LABELS_FAILED = 'The following orders had errors: %s.';

    /** @var Dhl_Versenden_Model_Log */
    protected $_logger;

    /**
     * Dhl_Versenden_Model_Cron constructor.
     *
     * Prepare dependencies:
     * - autoloader
     * - logger
     */
    public function __construct()
    {
        Mage::getSingleton('dhl_versenden/autoloader')->registerAutoload();

        $writer = Mage::getSingleton('dhl_versenden/logger_writer');
        $psrLogger = new Dhl_Versenden_Model_Logger_Mage($writer);

        $config = Mage::getModel('dhl_versenden/config');
        $dhlLogger = Mage::getSingleton('dhl_versenden/log', array('config' => $config));
        $dhlLogger->setLogger($psrLogger);
        $this->_logger = $dhlLogger;
    }

    /**
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function shipmentAutoCreate(Mage_Cron_Model_Schedule $schedule)
    {
        $config = Mage::getModel('dhl_versenden/config');

        $stores = array_filter(
            Mage::app()->getStores(),
            function (Mage_Core_Model_Store $store) use ($config) {
                return $config->isShipmentAutoCreateEnabled($store);
            }
        );

        $euCountries = explode(',', Mage::getStoreConfig(Mage_Core_Helper_Data::XML_PATH_EU_COUNTRIES_LIST));
        $orderStatus = $config->getAutoCreateOrderStatus();

        try {
            /** @var Dhl_Versenden_Model_Resource_Autocreate_Collection $collection */
            $collection = Mage::getResourceModel('dhl_versenden/autocreate_collection');
            $collection->addShippingMethodFilter();
            $collection->addShipmentFilter();
            $collection->addDeliveryCountriesFilter($euCountries);
            $collection->addStatusFilter($orderStatus);
            $collection->addStoreFilter($stores);

            // The DHL API only accepts up to 50 shipments per request, therefore we set this limit.
            $collection->setPageSize(50);
            
            /** @var Dhl_Versenden_Model_Shipping_Autocreate $autocreate */
            $autocreate = Mage::getSingleton('dhl_versenden/shipping_autocreate', array('logger' => $this->_logger));
            $num = $autocreate->autoCreate($collection);

            $scheduleMessage = sprintf(self::CRON_MESSAGE_LABELS_RETRIEVED, $num, $collection->getSize());

            /** @var Mage_Sales_Model_Order[] $failedOrders */
            $failedOrders = array_filter(
                $collection->getItems(),
                function (Mage_Sales_Model_Order $order) {
                    return !$order->hasShipments();
                }
            );

            if (!empty($failedOrders)) {
                $failedIncrements = array_map(
                    function (Mage_Sales_Model_Order $order) {
                        return $order->getIncrementId();
                    },
                    $failedOrders
                );

                $errorMessage = sprintf(self::CRON_MESSAGE_LABELS_FAILED, implode(', ', $failedIncrements));
                $schedule->setMessages($scheduleMessage . ' ' . $errorMessage);
                $schedule->setStatus(Mage_Cron_Model_Schedule::STATUS_ERROR);
            } else {
                $schedule->setMessages($scheduleMessage);
                $schedule->setStatus(Mage_Cron_Model_Schedule::STATUS_SUCCESS);
            }
        } catch (\Exception $e) {
            $this->_logger->error($e->getMessage(), array('exception' => $e));
            $schedule->setMessages($e->getMessage());
            $schedule->setStatus(Mage_Cron_Model_Schedule::STATUS_ERROR);
        }
    }
}
