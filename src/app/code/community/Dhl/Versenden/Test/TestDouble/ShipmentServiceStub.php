<?php

/**
 * Stub implementation of ShipmentServiceInterface for fast unit testing.
 *
 * This stub returns fake successful responses without making real HTTP calls to the DHL API.
 * Use this for:
 * - Unit tests of business logic
 * - Integration tests of Magento components
 * - Fast CI/CD feedback cycles
 *
 * Do NOT use this for:
 * - Service validation (use LiveApi tests instead)
 * - Testing actual DHL API behavior
 * - Discovering API limitations
 *
 * @see Dhl_Versenden_Test_Integration_LiveApi tests for real API validation
 */
class Dhl_Versenden_Test_TestDouble_ShipmentServiceStub implements \Dhl\Sdk\ParcelDe\Shipping\Api\ShipmentServiceInterface
{
    /**
     * API responses to return. Can be configured per test.
     *
     * @var \Dhl\Sdk\ParcelDe\Shipping\Api\Data\ShipmentInterface[]
     */
    public $responses = [];

    /**
     * Exception to throw (if set). Use for error scenario testing.
     *
     * @var \Exception|null
     */
    public $exception = null;

    /**
     * Captured requests for assertion. Access in tests to verify correct data.
     *
     * @var array
     */
    public $capturedRequests = [];

    /**
     * Captured configuration for assertion.
     *
     * @var \Dhl\Sdk\ParcelDe\Shipping\Api\Data\OrderConfigurationInterface|null
     */
    public $capturedConfiguration = null;

    /**
     * Counter for generating unique tracking numbers
     *
     * @var int
     */
    protected $_trackingCounter = 1;

    /**
     * Get SDK version (not implemented in stub)
     *
     * @return string
     */
    public function getVersion(): string
    {
        return '2.1.0-stub';
    }

    /**
     * Validate shipments (returns empty array - no validation in stub)
     *
     * @param array $shipmentOrders
     * @param \Dhl\Sdk\ParcelDe\Shipping\Api\Data\OrderConfigurationInterface|null $configuration
     * @return \Dhl\Sdk\ParcelDe\Shipping\Api\Data\ValidationResultInterface[]
     */
    public function validateShipments(
        array $shipmentOrders,
        ?\Dhl\Sdk\ParcelDe\Shipping\Api\Data\OrderConfigurationInterface $configuration = null
    ): array {
        return [];
    }

    /**
     * Create shipments (returns fake successful responses)
     *
     * @param array $shipmentOrders
     * @param \Dhl\Sdk\ParcelDe\Shipping\Api\Data\OrderConfigurationInterface|null $configuration
     * @return \Dhl\Sdk\ParcelDe\Shipping\Api\Data\ShipmentInterface[]
     * @throws \Exception If exception property is set
     */
    public function createShipments(
        array $shipmentOrders,
        ?\Dhl\Sdk\ParcelDe\Shipping\Api\Data\OrderConfigurationInterface $configuration = null
    ): array {
        // Capture requests for test assertions
        $this->capturedRequests = $shipmentOrders;
        $this->capturedConfiguration = $configuration;

        // Throw exception if configured for error testing
        if ($this->exception !== null) {
            throw $this->exception;
        }

        // Return pre-configured responses if set
        if (!empty($this->responses)) {
            return $this->responses;
        }

        // Generate default successful responses
        $shipments = [];
        foreach ($shipmentOrders as $index => $order) {
            $shipments[] = $this->_createFakeShipment($index);
        }

        return $shipments;
    }

    /**
     * Cancel shipments (returns fake successful cancellations)
     *
     * @param array $shipmentNumbers
     * @param string $profile
     * @return \Dhl\Sdk\ParcelDe\Shipping\Api\Data\CancellationResultInterface[]
     * @throws \Exception If exception property is set
     */
    public function cancelShipments(
        array $shipmentNumbers,
        string $profile = \Dhl\Sdk\ParcelDe\Shipping\Api\Data\OrderConfigurationInterface::DEFAULT_PROFILE
    ): array {
        // Capture requests for test assertions
        $this->capturedRequests = $shipmentNumbers;

        // Throw exception if configured for error testing
        if ($this->exception !== null) {
            throw $this->exception;
        }

        // Return shipment numbers (SDK returns string[] for successful cancellations)
        return $shipmentNumbers;
    }

    /**
     * Create a fake shipment response
     *
     * @param int $index Shipment index
     * @return \Dhl\Sdk\ParcelDe\Shipping\Service\ShipmentService\Shipment
     */
    protected function _createFakeShipment($index)
    {
        $shipmentNumber = sprintf('STUB%010d', $this->_trackingCounter++);
        $labelData = $this->_getFakePdfLabel();

        return new \Dhl\Sdk\ParcelDe\Shipping\Service\ShipmentService\Shipment(
            $index,
            $shipmentNumber,
            '', // No return shipment number
            $labelData,
            '', // No export document
            '', // No COD label
            '', // No QR code label
        );
    }

    /**
     * Get fake PDF label data (base64-encoded minimal PDF)
     *
     * @return string
     */
    protected function _getFakePdfLabel()
    {
        // Minimal valid PDF file (base64 encoded)
        return base64_decode(
            'JVBERi0xLjUKJbXtrvsKMyAwIG9iago8PCAvTGVuZ3RoIDQgMCBSCiAgIC9GaWx0ZXIgL0ZsYXRl' .
            'RGVjb2RlCj4+CnN0cmVhbQp4nCvkCuQCAAKSANcKZW5kc3RyZWFtCmVuZG9iago0IDAgb2JqCiAg' .
            'IDEyCmVuZG9iagoyIDAgb2JqCjw8Cj4+CmVuZG9iago1IDAgb2JqCjw8IC9UeXBlIC9QYWdlCiAg' .
            'IC9QYXJlbnQgMSAwIFIKICAgL01lZGlhQm94IFsgMCAwIDEwNCAxNDcgXQogICAvQ29udGVudHMg' .
            'MyAwIFIKICAgL0dyb3VwIDw8CiAgICAgIC9UeXBlIC9Hcm91cAogICAgICAvUyAvVHJhbnNwYXJl' .
            'bmN5CiAgICAgIC9DUyAvRGV2aWNlUkdCCiAgID4+CiAgIC9SZXNvdXJjZXMgMiAwIFIKPj4KZW5k' .
            'b2JqCjEgMCBvYmoKPDwgL1R5cGUgL1BhZ2VzCiAgIC9LaWRzIFsgNSAwIFIgXQogICAvQ291bnQg' .
            'MQo+PgplbmRvYmoKNiAwIG9iago8PCAvQ3JlYXRvciAoY2Fpcm8gMS45LjUgKGh0dHA6Ly9jYWly' .
            'b2dyYXBoaWNzLm9yZykpCiAgIC9Qcm9kdWNlciAoY2Fpcm8gMS45LjUgKGh0dHA6Ly9jYWlyb2dy' .
            'YXBoaWNzLm9yZykpCj4+CmVuZG9iago3IDAgb2JqCjw8IC9UeXBlIC9DYXRhbG9nCiAgIC9QYWdl' .
            'cyAxIDAgUgo+PgplbmRvYmoKeHJlZgowIDgKMDAwMDAwMDAwMCA2NTUzNSBmIAowMDAwMDAwMzQ2' .
            'IDAwMDAwIG4gCjAwMDAwMDAxMjUgMDAwMDAgbiAKMDAwMDAwMDAxNSAwMDAwMCBuIAowMDAwMDAw' .
            'MTA0IDAwMDAwIG4gCjAwMDAwMDAxNDYgMDAwMDAgbiAKMDAwMDAwMDQxMSAwMDAwMCBuIAowMDAw' .
            'MDAwNTM2IDAwMDAwIG4gCnRyYWlsZXIKPDwgL1NpemUgOAogICAvUm9vdCA3IDAgUgogICAvSW5m' .
            'byA2IDAgUgo+PgpzdGFydHhyZWYKNTg4CiUlRU9GCg==',
        );
    }

    /**
     * Configure stub to return specific responses
     *
     * @param array $responses Array of Shipment objects to return
     * @return self
     */
    public function setResponses(array $responses)
    {
        $this->responses = $responses;
        return $this;
    }

    /**
     * Configure stub to throw an exception
     *
     * @param \Exception $exception Exception to throw
     * @return self
     */
    public function setException(\Exception $exception)
    {
        $this->exception = $exception;
        return $this;
    }

    /**
     * Reset stub state between tests
     *
     * @return self
     */
    public function reset()
    {
        $this->responses = [];
        $this->exception = null;
        $this->capturedRequests = [];
        $this->capturedConfiguration = null;
        $this->_trackingCounter = 1;
        return $this;
    }
}
