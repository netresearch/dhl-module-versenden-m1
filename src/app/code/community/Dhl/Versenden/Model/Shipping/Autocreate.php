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
 */

/**
 * Dhl_Versenden_Model_Shipping_Autocreate
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Model_Shipping_Autocreate
{
    /** @var Dhl_Versenden_Model_Log */
    protected $logger;

    /**
     * Dhl_Versenden_Model_Shipping_Autocreate constructor.
     */
    public function __construct($args = array())
    {
        if (!isset($args['logger']) || !$args['logger'] instanceof Dhl_Versenden_Model_Log) {
            Mage::throwException('missing or invalid argument: logger');
        }

        $this->logger = $args['logger'];
    }

    /**
     * @param Mage_Sales_Model_Resource_Order_Collection $collection
     * @return Mage_Shipping_Model_Shipment_Request[]
     */
    protected function prepareShipmentRequests(Mage_Sales_Model_Resource_Order_Collection $collection)
    {
        $shipmentRequests = array();

        $shipmentConfig = Mage::getModel('dhl_versenden/config_shipment');
        $shipperConfig  = Mage::getModel('dhl_versenden/config_shipper');
        $serviceConfig  = Mage::getModel('dhl_versenden/config_service');

        /** @var Mage_Sales_Model_Order $order */
        foreach ($collection as $order) {
            try {
                $shipment = $order->prepareShipment();
                $shipment->register();

                $builder = new Dhl_Versenden_Model_Shipping_Autocreate_Builder(
                    $order,
                    $shipmentConfig,
                    $shipperConfig,
                    $serviceConfig
                );
                $request = $builder->createShipmentRequest($shipment);

                $shipmentRequests[$order->getId()] = $request;
            } catch (Exception $e) {
                $this->logger->error($e->getMessage(), array('exception' => $e));
            }
        }

        return $shipmentRequests;
    }

    /**
     * @param Mage_Sales_Model_Resource_Order_Collection $collection
     * @return int The number of successfully created shipment orders.
     */
    public function autoCreate(Mage_Sales_Model_Resource_Order_Collection $collection)
    {
        if (!$collection->getSize()) {
            return 0;
        }

        $ordersShipped = 0;
        $shipments = array();

        $shipmentRequests = $this->prepareShipmentRequests($collection);
        $gateway = Mage::getModel('dhl_versenden/webservice_gateway_soap');
        $result = $gateway->createShipmentOrder($shipmentRequests);

        $carrier     = Mage::getModel('dhl_versenden/shipping_carrier_versenden');
        $pdfLib      = new \Dhl\Versenden\Bcs\Api\Pdf\Adapter\Zend();
        $transaction = Mage::getModel('core/resource_transaction');

        /** @var Mage_Shipping_Model_Shipment_Request $shipmentRequest */
        foreach ($shipmentRequests as $orderId => $shipmentRequest) {
            $shipment = $shipmentRequest->getOrderShipment();

            // handle request validation errors, no label request was sent
            if ($shipmentRequest->hasData('request_data_exception')) {
                Mage::helper('dhl_versenden/data')->addStatusHistoryComment(
                    $shipment->getOrder(),
                    $shipmentRequest->getData('request_data_exception')
                );
                $incId = $shipment->getOrder()->getIncrementId();
                $message = __('Autocreation Error');
                $message .= "\n";
                $message .= ' # '. $incId . ' : ' . $shipmentRequest->getData('request_data_exception');
                $this->logger->error($message);
                continue;
            }

            // handle webservice errors, label response includes item status
            $shipmentStatus = $result->getCreatedItems()->getItem($orderId)->getStatus();
            if ($shipmentStatus->isError()) {
                Mage::helper('dhl_versenden/data')->addStatusHistoryComment(
                    $shipmentRequest->getOrderShipment()->getOrder(),
                    sprintf('%s %s', $shipmentStatus->getStatusText(), $shipmentStatus->getStatusMessage())
                );
                continue;
            }

            $labels = $result->getCreatedItems()->getItem($orderId)->getAllLabels($pdfLib);
            $shipment->setShippingLabel($labels);
            $track = Mage::getModel('sales/order_shipment_track')
                ->setNumber($result->getShipmentNumber($orderId))
                ->setCarrierCode($carrier->getCarrierCode())
                ->setTitle($carrier->getConfigData('title'));
            $shipment->addTrack($track);
            $shipment->getOrder()->setIsInProcess(true);
            $transaction
                ->addObject($shipment)
                ->addObject($shipment->getOrder());

            $ordersShipped++;
            $shipments[] = $shipment;
        }

        $transaction->save();

        /** @var Mage_Sales_Model_Order_Shipment $shipment */
        foreach ($shipments as $shipment) {
            /** @var DHL_Versenden_Model_Config $config */
            $config = Mage::getModel('dhl_versenden/config');
            $notifyCustomer = $config->isAutoCreateNotifyCustomer($shipment->getStoreId());
            if ($notifyCustomer && $shipment->getIncrementId()) {
                $shipment->sendEmail(true)->setEmailSent(true);
            }
        }

        return $ordersShipped;
    }
}
