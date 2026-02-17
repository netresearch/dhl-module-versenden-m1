<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Model_Shipping_Autocreate
{
    /** @var Dhl_Versenden_Model_Log */
    protected $logger;

    /**
     * Dhl_Versenden_Model_Shipping_Autocreate constructor.
     */
    public function __construct($args = [])
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
        $shipmentRequests = [];

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
                    $serviceConfig,
                );
                $request = $builder->createShipmentRequest($shipment);

                $shipmentRequests[$order->getId()] = $request;
            } catch (Exception $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
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

        $shipmentRequests = $this->prepareShipmentRequests($collection);

        // Filter out requests with validation errors
        $validRequests = [];
        $requestMap = [];

        foreach ($shipmentRequests as $orderId => $request) {
            if ($request->hasData('request_data_exception')) {
                continue;
            }
            $validRequests[] = $request;
            $requestMap[] = $orderId;
        }

        // Call REST client
        $client = Mage::getModel('dhl_versenden/webservice_client_shipment');

        $firstRequest = reset($validRequests);
        $storeId = $firstRequest ? $firstRequest->getOrderShipment()->getStoreId() : null;

        $factory = Mage::getModel('dhl_versenden/webservice_builder_factory');
        $orderConfig = $factory->createSettingsBuilder()->build($storeId);

        try {
            $createdShipments = $client->createShipments($validRequests, $orderConfig);
        } catch (\Dhl\Sdk\ParcelDe\Shipping\Exception\ServiceException $e) {
            $this->logger->error('Bulk shipment creation failed: ' . $e->getMessage());
            return 0;
        }

        // Process results individually
        $carrier = Mage::getModel('dhl_versenden/shipping_carrier_versenden');
        $pdfAdapter = new \Dhl\Versenden\ParcelDe\Pdf\Adapter\Zend();

        $shipments = [];
        foreach ($createdShipments as $index => $apiResult) {
            $orderId = $requestMap[$index];
            $request = $shipmentRequests[$orderId];

            $shipment = $this->processShipmentResult($request, $apiResult, $carrier, $pdfAdapter);
            if ($shipment !== null) {
                $shipments[] = $shipment;
            }
        }

        $this->sendCustomerNotifications($shipments);

        return count($shipments);
    }

    /**
     * Process a single shipment result from the API.
     *
     * @param Mage_Shipping_Model_Shipment_Request $request
     * @param \Dhl\Sdk\ParcelDe\Shipping\Api\Data\ShipmentInterface|null $apiResult
     * @param Dhl_Versenden_Model_Shipping_Carrier_Versenden $carrier
     * @param \Dhl\Versenden\ParcelDe\Pdf\Adapter\Zend $pdfAdapter
     * @return Mage_Sales_Model_Order_Shipment|null
     */
    protected function processShipmentResult($request, $apiResult, $carrier, $pdfAdapter)
    {
        $shipment = $request->getOrderShipment();
        $order = $shipment->getOrder();

        if ($request->hasData('request_data_exception')) {
            $this->logError($order, $request->getData('request_data_exception'));
            return null;
        }

        if ($apiResult === null) {
            $this->logError($order, 'Shipment creation failed (no label returned)');
            return null;
        }

        // Process labels and tracking
        $labelPages = array_map('base64_decode', array_filter($apiResult->getLabels()));
        $shipment->setShippingLabel($pdfAdapter->merge($labelPages));

        $track = Mage::getModel('sales/order_shipment_track')
            ->setNumber($apiResult->getShipmentNumber())
            ->setCarrierCode($carrier->getCarrierCode())
            ->setTitle($carrier->getConfigData('title'));
        $shipment->addTrack($track);
        $order->setIsInProcess(true);

        // Save individually - one failure doesn't affect others
        try {
            $shipment->save();
            $order->save();
            return $shipment;
        } catch (Exception $e) {
            $this->logError($order, "DB save failed (orphaned label {$apiResult->getShipmentNumber()}): " . $e->getMessage());
            return null;
        }
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @param string $message
     */
    protected function logError($order, $message)
    {
        Mage::helper('dhl_versenden/data')->addStatusHistoryComment($order, $message);
        $this->logger->error("Autocreation Error\n # {$order->getIncrementId()} : {$message}");
    }

    /**
     * @param Mage_Sales_Model_Order_Shipment[] $shipments
     */
    protected function sendCustomerNotifications(array $shipments)
    {
        $config = Mage::getModel('dhl_versenden/config');
        foreach ($shipments as $shipment) {
            if ($config->isAutoCreateNotifyCustomer($shipment->getStoreId()) && $shipment->getIncrementId()) {
                $shipment->sendEmail(true)->setEmailSent(true);
            }
        }
    }
}
