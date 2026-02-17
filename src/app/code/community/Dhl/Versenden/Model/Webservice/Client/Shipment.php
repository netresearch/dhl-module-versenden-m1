<?php

/**
 * See LICENSE.md for license details.
 */

use Dhl\Sdk\ParcelDe\Shipping\Api\Data\OrderConfigurationInterface;
use Dhl\Sdk\ParcelDe\Shipping\Api\ShipmentServiceInterface;
use Dhl\Sdk\ParcelDe\Shipping\Auth\AuthenticationStorage;
use Dhl\Sdk\ParcelDe\Shipping\Exception\DetailedServiceException;
use Dhl\Sdk\ParcelDe\Shipping\Exception\ServiceException;
use Dhl\Sdk\ParcelDe\Shipping\Service\ServiceFactory;
use Dhl\Versenden\ParcelDe\Config\ValidationException;
use Psr\Log\LoggerInterface;

/**
 * DHL REST Shipment Client
 *
 * This class integrates with the DHL Parcel DE Shipping SDK to create and cancel shipments
 * using the REST API. It replaces the legacy SOAP gateway.
 */
class Dhl_Versenden_Model_Webservice_Client_Shipment
{
    /**
     * @var ShipmentServiceInterface
     */
    protected $_service;

    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @var Dhl_Versenden_Model_Config_Shipper
     */
    protected $_config;

    /**
     * Initialize the shipment client with SDK service.
     *
     * Supports two usage patterns:
     * 1. Magento pattern: Mage::getModel('dhl_versenden/webservice_client_shipment') - passes array
     * 2. DI pattern: new Dhl_Versenden_Model_Webservice_Client_Shipment($service) - passes service
     *
     * @param ShipmentServiceInterface|array|null $serviceOrArgs Optional service for DI or Magento args array
     * @param LoggerInterface|null $logger Optional logger for DI/testing
     * @param Dhl_Versenden_Model_Config_Shipper|null $config Optional config for DI/testing
     */
    public function __construct(
        $serviceOrArgs = null,
        LoggerInterface $logger = null,
        Dhl_Versenden_Model_Config_Shipper $config = null
    ) {
        // Handle Magento model pattern (array passed as first arg) vs DI pattern (service passed)
        $service = null;
        if ($serviceOrArgs instanceof ShipmentServiceInterface) {
            $service = $serviceOrArgs;
        }
        // If array passed (Magento pattern), ignore it - we create dependencies internally

        $this->_config = $config ?? Mage::getModel('dhl_versenden/config_shipper');
        $this->_logger = $logger ?? $this->_createLogger();
        $this->_service = $service ?? $this->_createService();
    }

    /**
     * Create config-aware, sanitizing PSR-3 logger instance for SDK
     *
     * Creates a PSR-3 logger chain with automatic sanitization:
     * 1. Dhl_Versenden_Model_Logger_ConfigAware - Checks isLoggingEnabled() FIRST (performance optimization)
     * 2. Dhl_Versenden_Model_Logger_Sanitizing - Masks PII and credentials (GDPR compliance)
     * 3. Dhl_Versenden_Model_Logger_Mage - PSR-3 adapter that writes to file
     *
     * Config check happens first to skip expensive sanitization when logging is disabled.
     * SDK's LoggerPlugin logs full HTTP requests/responses through this logger chain.
     * All sensitive data is automatically masked before reaching log files.
     *
     * @return LoggerInterface
     */
    protected function _createLogger()
    {
        // Create base PSR-3 logger (writes to file via Mage::log)
        $writer = Mage::getModel('dhl_versenden/logger_writer');
        $fileLogger = new Dhl_Versenden_Model_Logger_Mage($writer);

        // Wrap with sanitizing logger (masks PII and credentials for GDPR compliance)
        $sanitizer = new Dhl_Versenden_Model_Logger_Sanitizer();
        $sanitizingLogger = new Dhl_Versenden_Model_Logger_Sanitizing($fileLogger, $sanitizer);

        // Wrap with config-aware PSR-3 logger (checks logging_enabled and log_level FIRST)
        // This ensures sanitization only happens when logging is actually enabled (performance optimization)
        $config = Mage::getModel('dhl_versenden/config');
        $configAwareLogger = new Dhl_Versenden_Model_Logger_ConfigAware($sanitizingLogger, $config);

        return $configAwareLogger;
    }

    /**
     * Create the SDK shipment service with authentication and HTTP client
     *
     * @return ShipmentServiceInterface
     */
    protected function _createService()
    {
        // Create authentication storage with app token and user credentials
        $accountSettings = $this->_config->getAccountSettings();
        $auth = new AuthenticationStorage(
            $this->_config->getAppToken(),
            $accountSettings->getUser(),
            $accountSettings->getSignature(),
        );

        // Create SDK service factory and service
        $factory = new ServiceFactory();
        $sandboxMode = $this->_config->isSandboxModeEnabled();

        return $factory->createShipmentService($auth, $this->_logger, $sandboxMode);
    }

    /**
     * Create shipments via DHL REST API
     *
     * Follows the same pattern as SOAP Gateway: accepts Magento request objects,
     * converts them internally to SDK objects, fires events with original Magento objects.
     *
     * @param Mage_Shipping_Model_Shipment_Request[] $magentoRequests Magento shipment requests
     * @param OrderConfigurationInterface|null $orderConfig Order configuration
     * @return array Array of shipment responses indexed by sequence number
     * @throws DetailedServiceException When detailed error information is available
     * @throws ServiceException When a generic service error occurs
     */
    public function createShipments(array $magentoRequests, $orderConfig = null)
    {
        // Dispatch BEFORE event (for observers that need to prepare or validate)
        $eventData = ['shipment_requests' => $magentoRequests];
        Mage::dispatchEvent('dhl_versenden_create_shipment_order_before', $eventData);

        try {
            // Convert Magento requests to SDK shipment orders (like SOAP Gateway's prepareShipmentOrders)
            $sdkShipments = $this->_convertToSdkShipments($magentoRequests);

            // Empty collection guard - Restore SOAP Gateway pattern (Gateway/Abstract.php:98-100)
            // If all requests failed validation, return empty result instead of calling API
            if (empty($sdkShipments)) {
                $this->_logger->warning('All shipment requests failed validation, skipping API call');
                return [];
            }

            // Call REST SDK
            $shipments = $this->_service->createShipments($sdkShipments, $orderConfig);

            // Dispatch AFTER event with ORIGINAL Magento requests (not SDK objects!)
            // This maintains backward compatibility with existing observers
            $eventData = ['request_data' => $magentoRequests, 'result' => $shipments];
            Mage::dispatchEvent('dhl_versenden_create_shipment_order_after', $eventData);

            return $shipments;
        } catch (DetailedServiceException $e) {
            $this->_logger->error(
                'Detailed shipment creation error: ' . $e->getMessage(),
                ['exception' => $e],
            );
            throw $e;
        } catch (ServiceException $e) {
            $this->_logger->error(
                'Shipment creation failed: ' . $e->getMessage(),
                ['exception' => $e],
            );
            throw $e;
        }
    }

    /**
     * Convert Magento shipment requests to SDK shipment order objects.
     *
     * This method performs the same role as SOAP Gateway's prepareShipmentOrders():
     * - Validates business rules (restored from SOAP Gateway)
     * - Accepts Magento-native request objects
     * - Extracts data and converts to SDK format
     * - Returns SDK-ready objects for API calls
     *
     * Validation happens BEFORE SDK build (fail fast) because REST SDK objects
     * are write-only DTOs with no getters for inspection.
     *
     * @param Mage_Shipping_Model_Shipment_Request[] $magentoRequests
     * @return array SDK Shipment objects
     */
    protected function _convertToSdkShipments(array $magentoRequests)
    {
        $sdkShipments = [];
        $orderBuilder = $this->_createOrderBuilder();
        $validator = Mage::getSingleton('dhl_versenden/webservice_builder_validator');

        foreach ($magentoRequests as $sequenceNumber => $magentoRequest) {
            try {
                // VALIDATE FIRST - Restore SOAP Gateway validation pattern
                // Validates business rules before expensive SDK build operation
                $validator->validate($magentoRequest);

                // Create SDK builder for this shipment
                $sdkBuilder = new \Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder();

                // Extract data from Magento request
                $shipment = $magentoRequest->getOrderShipment();
                $packageInfo = $magentoRequest->getPackages() ?? [];
                $serviceInfo = $magentoRequest->getData('services') ?? [];
                $customsInfo = $magentoRequest->getData('customs') ?? [];
                $product = $magentoRequest->getData('gk_api_product');

                // Use OrderBuilder to populate SDK builder (same as Carrier did)
                $orderBuilder->build(
                    $sdkBuilder,
                    $shipment,
                    $packageInfo,
                    $serviceInfo,
                    $customsInfo,
                    $product,
                );

                // Create SDK shipment object
                $sdkShipments[$sequenceNumber] = $sdkBuilder->create();

            } catch (ValidationException $e) {
                // Graceful error storage - Restore SOAP Gateway pattern (Gateway/Abstract.php:87-92)
                // Stores validation error in request for later display to merchant
                $magentoRequest->setData(
                    'request_data_exception',
                    Mage::helper('dhl_versenden/data')->__($e->getMessage()),
                );

                $this->_logger->warning(
                    'Shipment validation failed: ' . $e->getMessage(),
                    ['sequence' => $sequenceNumber],
                );
            }
        }

        return $sdkShipments;
    }

    /**
     * Create the OrderBuilder with all required dependencies.
     *
     * @return Dhl_Versenden_Model_Webservice_Builder_Order
     */
    protected function _createOrderBuilder()
    {
        $factory = Mage::getModel('dhl_versenden/webservice_builder_factory');
        return $factory->createOrderBuilder();
    }

    /**
     * Validate shipments via DHL REST API without booking labels
     *
     * This method validates shipment data against the DHL API without actually
     * creating shipments or booking tracking numbers. Useful for pre-validation
     * before committing to shipment creation.
     *
     * @param array $magentoRequests Array of Mage_Shipping_Model_Shipment_Request objects
     * @param OrderConfigurationInterface|null $orderConfig Optional order configuration
     * @return array Array of validation responses
     * @throws DetailedServiceException When detailed error information is available
     * @throws ServiceException When a generic service error occurs
     */
    public function validateShipments(array $magentoRequests, $orderConfig = null)
    {
        // Dispatch BEFORE event (for observers that need to prepare or validate)
        $eventData = ['request' => $magentoRequests];
        Mage::dispatchEvent('dhl_versenden_validate_shipment_order_before', $eventData);

        // Build SDK request objects using the same logic as createShipments
        $sdkRequests = $this->_convertToSdkShipments($magentoRequests);

        // Empty collection guard - same as createShipments
        if (empty($sdkRequests)) {
            $this->_logger->warning('All validation requests failed conversion, skipping API call');
            return [];
        }

        try {
            // Validate shipments using SDK's validateShipments method
            $validationResults = $this->_service->validateShipments($sdkRequests, $orderConfig);

            // Dispatch AFTER event (for logging and other post-processing)
            $eventData = ['request_data' => $magentoRequests, 'result' => $validationResults];
            Mage::dispatchEvent('dhl_versenden_validate_shipment_order_after', $eventData);

            return $validationResults;
        } catch (DetailedServiceException $e) {
            $this->_logger->error(
                'Detailed validation error: ' . $e->getMessage(),
                ['exception' => $e],
            );
            throw $e;
        } catch (ServiceException $e) {
            $this->_logger->error(
                'Validation failed: ' . $e->getMessage(),
                ['exception' => $e],
            );
            throw $e;
        }
    }

    /**
     * Cancel shipments via DHL REST API
     *
     * Follows the same event dispatch pattern as the legacy SOAP Gateway to maintain
     * compatibility with existing observers (e.g., label status tracking).
     *
     * @param array $shipmentNumbers Array of shipment numbers to cancel
     * @return array Array of cancellation responses
     * @throws DetailedServiceException When detailed error information is available
     * @throws ServiceException When a generic service error occurs
     */
    public function cancelShipments(array $shipmentNumbers)
    {
        // Dispatch BEFORE event (for observers that need to prepare or validate)
        $eventData = ['shipment_numbers' => $shipmentNumbers];
        Mage::dispatchEvent('dhl_versenden_delete_shipment_order_before', $eventData);

        try {
            $cancelled = $this->_service->cancelShipments($shipmentNumbers);

            // Dispatch AFTER event (for label status tracking and other post-processing)
            $eventData = ['request_data' => $shipmentNumbers, 'result' => $cancelled];
            Mage::dispatchEvent('dhl_versenden_delete_shipment_order_after', $eventData);

            return $cancelled;
        } catch (DetailedServiceException $e) {
            $this->_logger->error(
                'Detailed cancellation error: ' . $e->getMessage(),
                ['exception' => $e],
            );
            throw $e;
        } catch (ServiceException $e) {
            $this->_logger->error(
                'Cancellation failed: ' . $e->getMessage(),
                ['exception' => $e],
            );
            throw $e;
        }
    }
}
