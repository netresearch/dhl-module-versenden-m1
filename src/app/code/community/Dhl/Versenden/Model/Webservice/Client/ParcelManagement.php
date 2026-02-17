<?php

/**
 * See LICENSE.md for license details.
 */

use Dhl\Versenden\Cig\Api\CheckoutApi;
use Dhl\Versenden\Cig\ApiException;
use Dhl\Versenden\Cig\Configuration as CigConfiguration;

/**
 * DHL Parcel Management Client
 *
 * Wrapper for CIG (Checkout Information Gateway) API to query available delivery services.
 * Accidentally deleted in cd8f7b3, restored with exception handling and config-aware logging.
 */
class Dhl_Versenden_Model_Webservice_Client_ParcelManagement
{
    /**
     * @var Dhl_Versenden_Model_Config_Shipper
     */
    protected $config;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    public function __construct()
    {
        $this->config = Mage::getModel('dhl_versenden/config_shipper');
        $this->logger = $this->_createLogger();
    }

    /**
     * Create config-aware, sanitizing PSR-3 logger instance
     *
     * Creates a PSR-3 logger chain with automatic sanitization:
     * 1. Dhl_Versenden_Model_Logger_ConfigAware - Checks isLoggingEnabled() FIRST (performance optimization)
     * 2. Dhl_Versenden_Model_Logger_Sanitizing - Masks PII and credentials (GDPR compliance)
     * 3. Dhl_Versenden_Model_Logger_Mage - PSR-3 adapter that writes to file
     *
     * Config check happens first to skip expensive sanitization when logging is disabled.
     * Manual logging with structured context arrays goes through this sanitizing chain.
     * All sensitive data is automatically masked before reaching log files.
     *
     * @return \Psr\Log\LoggerInterface
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
     * Get available delivery services for recipient ZIP code
     *
     * @param string $date Date in format 'Y-m-d H:i:s'
     * @param string $zip Recipient ZIP code
     * @return \Dhl\Versenden\Cig\Model\AvailableServicesMap
     * @throws Mage_Core_Exception
     */
    public function checkoutRecipientZipAvailableServicesGet($date, $zip)
    {
        try {
            $account = $this->config->getAccountSettings();
            $ekp = $account->getEkp();
            $user = $this->config->getWebserviceAuthUsername();
            $signature = $this->config->getWebserviceAuthPassword();

            $cigConfig = new CigConfiguration();
            $cigConfig->setApiKey(
                'DPDHL-User-Authentication-Token',
                base64_encode($this->config->getParcelmanagementApiKey()),
            );
            $cigConfig->setUsername($user);
            $cigConfig->setPassword($signature);
            $cigConfig->setHost($this->config->getParcelManagementEndpoint());

            // Prepare request details for logging
            $endpoint = $cigConfig->getHost() . '/checkout/' . $zip . '/availableServices';
            $dateObj = new \DateTime($date);
            $startTime = microtime(true);

            // Log request with structured context (automatic sanitization by Sanitizing logger)
            $this->logger->debug('ParcelManagement API Request', [
                'method' => 'GET',
                'url' => $endpoint,
                'query' => ['startDate' => $dateObj->format('Y-m-d')],
                'headers' => [
                    'X-EKP' => $ekp,
                    'DPDHL-User-Authentication-Token' => $cigConfig->getApiKey('DPDHL-User-Authentication-Token'),
                ],
                'zip' => $zip,
            ]);

            $client = new CheckoutApi($cigConfig);
            $response = $client->checkoutRecipientZipAvailableServicesGet($ekp, $zip, $dateObj);

            // Log successful response with duration and response data
            $duration = microtime(true) - $startTime;
            $this->logger->debug('ParcelManagement API Response', [
                'status' => 'success',
                'status_code' => 200,
                'zip' => $zip,
                'duration_ms' => round($duration * 1000, 2),
                'response' => $response, // SDK object, will be sanitized if it has toArray/jsonSerialize
            ]);

            return $response;

        } catch (ApiException $e) {
            // Log API exception with structured context and exception details
            $this->logger->error('ParcelManagement API Error', [
                'error_message' => $e->getMessage(),
                'status_code' => $e->getCode(),
                'response_body' => $e->getResponseBody(),
                'zip' => $zip,
                'exception' => $e, // PSR-3 standard: exception in context
            ]);

            Mage::throwException(
                'Parcel Management API error: ' . $e->getMessage(),
            );
        } catch (\Exception $e) {
            // Log general exception with structured context
            $this->logger->error('ParcelManagement Client Error', [
                'error_message' => $e->getMessage(),
                'exception_class' => get_class($e),
                'zip' => $zip,
                'exception' => $e, // PSR-3 standard: exception in context
            ]);

            Mage::throwException(
                'Failed to query available services: ' . $e->getMessage(),
            );
        }
    }
}
