<?php

/**
 * See LICENSE.md for license details.
 */


class Dhl_Versenden_Model_Webservice_Builder_Order
{
    /** @var Dhl_Versenden_Model_Webservice_Builder_Shipper */
    protected $_shipperBuilder;
    /** @var Dhl_Versenden_Model_Webservice_Builder_Receiver */
    protected $_receiverBuilder;
    /** @var Dhl_Versenden_Model_Webservice_Builder_Service */
    protected $_serviceBuilder;
    /** @var Dhl_Versenden_Model_Webservice_Builder_Package */
    protected $_packageBuilder;
    /** @var Dhl_Versenden_Model_Webservice_Builder_Customs */
    protected $_customsBuilder;
    /** @var Dhl_Versenden_Model_Webservice_Builder_Settings */
    protected $_settingsBuilder;
    /** @var Dhl_Versenden_Model_Info_Builder */
    protected $_infoBuilder;

    /**
     * Dhl_Versenden_Model_Webservice_Builder_Order constructor.
     *
     * @param mixed[] $args
     * @throws Mage_Core_Exception
     */
    public function __construct($args)
    {
        $argDef = [
            'shipper_builder'  => 'Dhl_Versenden_Model_Webservice_Builder_Shipper',
            'receiver_builder' => 'Dhl_Versenden_Model_Webservice_Builder_Receiver',
            'service_builder'  => 'Dhl_Versenden_Model_Webservice_Builder_Service',
            'package_builder'  => 'Dhl_Versenden_Model_Webservice_Builder_Package',
            'customs_builder'  => 'Dhl_Versenden_Model_Webservice_Builder_Customs',
            'settings_builder' => 'Dhl_Versenden_Model_Webservice_Builder_Settings',
            'info_builder'     => 'Dhl_Versenden_Model_Info_Builder',
        ];

        $missingArguments = array_diff_key($argDef, $args);
        if (!empty($missingArguments)) {
            $message = sprintf('required arguments missing: %s', implode(', ', array_keys($missingArguments)));
            Mage::throwException($message);
        }

        $invalidArguments = [];
        foreach (array_keys($argDef) as $key) {
            if (!is_a($args[$key], $argDef[$key])) {
                $invalidArguments[] = $key;
            }
        }

        if (!empty($invalidArguments)) {
            $message = sprintf('invalid arguments: %s', implode(', ', $invalidArguments));
            Mage::throwException($message);
        }

        $this->_shipperBuilder = $args['shipper_builder'];
        $this->_receiverBuilder = $args['receiver_builder'];
        $this->_serviceBuilder = $args['service_builder'];
        $this->_packageBuilder = $args['package_builder'];
        $this->_customsBuilder = $args['customs_builder'];
        $this->_settingsBuilder = $args['settings_builder'];
        $this->_infoBuilder = $args['info_builder'];
    }

    /**
     * Build shipment order request by populating the SDK builder.
     *
     * This method orchestrates all sub-builders to populate the SDK builder with shipment data.
     * It follows the same pattern as getShipmentOrder() but uses the REST SDK builder pattern.
     *
     * @param \Dhl\Sdk\ParcelDe\Shipping\Api\ShipmentOrderRequestBuilderInterface $sdkBuilder
     * @param Mage_Sales_Model_Order_Shipment $shipment
     * @param string[] $packageInfo
     * @param array<string, mixed> $serviceInfo
     * @param string[] $customsInfo
     * @param string $gkApiProduct DHL product code (e.g., 'V01PAK')
     * @return void
     */
    public function build(
        \Dhl\Sdk\ParcelDe\Shipping\Api\ShipmentOrderRequestBuilderInterface $sdkBuilder,
        Mage_Sales_Model_Order_Shipment $shipment,
        array $packageInfo,
        array $serviceInfo,
        array $customsInfo,
        $gkApiProduct
    ) {
        // 1. Build shipper data (with dynamic billing number based on product code)
        $includeReturnShipment = !empty($serviceInfo['shipment_service'][\Dhl\Versenden\ParcelDe\Service\ReturnShipment::CODE]);
        $this->_shipperBuilder->setShipment($shipment);
        $this->_shipperBuilder->build($sdkBuilder, $shipment->getStoreId(), $gkApiProduct, $includeReturnShipment);

        // 2. Build receiver data (email conditional on parcelAnnouncement or CDP —
        //    DHL sends parcel notifications when a recipient email is present,
        //    and CDP delivery requires email for drop-point notifications)
        $isCdpDelivery = !empty($serviceInfo['shipment_service'][\Dhl\Versenden\ParcelDe\Service\ClosestDropPoint::CODE])
            || ($serviceInfo['service_setting'][\Dhl\Versenden\ParcelDe\Service\DeliveryType::CODE] ?? '') === \Dhl\Versenden\ParcelDe\Service\DeliveryType::CDP;
        $includeRecipientEmail = !empty($serviceInfo['shipment_service'][\Dhl\Versenden\ParcelDe\Service\ParcelAnnouncement::CODE])
            || $isCdpDelivery;
        $this->_receiverBuilder->build($sdkBuilder, $shipment->getShippingAddress(), $includeRecipientEmail);

        // 3. Build service selection
        $this->_serviceBuilder->build($sdkBuilder, $shipment->getOrder(), $serviceInfo);

        // 4. Build package details
        $this->_packageBuilder->build($sdkBuilder, $packageInfo);

        // 5. Build customs data (for international shipments)
        /** @var Mage_Sales_Model_Resource_Order_Invoice_Collection $invoiceCollection */
        $invoiceCollection = $shipment->getOrder()->getInvoiceCollection();
        /** @var Mage_Sales_Model_Order_Invoice $invoice */
        $invoice = $invoiceCollection->getFirstItem();

        $this->_customsBuilder->build(
            $sdkBuilder,
            $invoice->getIncrementId(),
            $customsInfo,
            $packageInfo,
        );

        // 6. Set shipment details (product code, date, reference)
        $shipmentDate = new \DateTime(Mage::helper('dhl_versenden/data')->utcToCet(null, 'Y-m-d'));
        $sdkBuilder->setShipmentDetails(
            $gkApiProduct,
            $shipmentDate,
            $shipment->getOrder()->getIncrementId(),
        );

        // Note: Settings builder handles OrderConfiguration separately in the REST client
        // It's not part of the per-shipment builder pattern
    }
}
