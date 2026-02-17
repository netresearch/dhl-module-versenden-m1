<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Test_Model_Webservice_Builder_OrderTest extends EcomDev_PHPUnit_Test_Case
{
    protected function getBuilders()
    {
        $shipperBuilder = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Builder_Shipper::class)
            ->disableOriginalConstructor()
            ->getMock();
        $receiverBuilder = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Builder_Receiver::class)
            ->disableOriginalConstructor()
            ->getMock();
        $serviceBuilder = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Builder_Service::class)
            ->disableOriginalConstructor()
            ->getMock();
        $packageBuilder = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Builder_Package::class)
            ->disableOriginalConstructor()
            ->getMock();
        $customsBuilder = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Builder_Customs::class)
            ->disableOriginalConstructor()
            ->getMock();
        $settingsBuilder = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Builder_Settings::class)
            ->disableOriginalConstructor()
            ->getMock();

        return [
            'shipper_builder' => $shipperBuilder,
            'receiver_builder' => $receiverBuilder,
            'service_builder' => $serviceBuilder,
            'package_builder' => $packageBuilder,
            'customs_builder' => $customsBuilder,
            'settings_builder' => $settingsBuilder,
        ];
    }

    /**
     * @test
     */
    public function constructorArgShipperBuilderMissing()
    {
        $this->expectException(Mage_Core_Exception::class);

        $args = $this->getBuilders();
        unset($args['shipper_builder']);

        new Dhl_Versenden_Model_Webservice_Builder_Order($args);
    }

    /**
     * @test
     */
    public function constructorArgShipperBuilderWrongType()
    {
        $this->expectException(Mage_Core_Exception::class);

        $args = $this->getBuilders();
        $args['shipper_builder'] = new stdClass();

        new Dhl_Versenden_Model_Webservice_Builder_Order($args);
    }

    /**
     * @test
     */
    public function constructorArgReceiverBuilderMissing()
    {
        $this->expectException(Mage_Core_Exception::class);

        $args = $this->getBuilders();
        unset($args['receiver_builder']);

        new Dhl_Versenden_Model_Webservice_Builder_Order($args);
    }

    /**
     * @test
     */
    public function constructorArgReceiverBuilderWrongType()
    {
        $this->expectException(Mage_Core_Exception::class);

        $args = $this->getBuilders();
        $args['receiver_builder'] = new stdClass();

        new Dhl_Versenden_Model_Webservice_Builder_Order($args);
    }

    /**
     * @test
     */
    public function constructorArgServiceBuilderMissing()
    {
        $this->expectException(Mage_Core_Exception::class);

        $args = $this->getBuilders();
        unset($args['service_builder']);

        new Dhl_Versenden_Model_Webservice_Builder_Order($args);
    }

    /**
     * @test
     */
    public function constructorArgServiceBuilderWrongType()
    {
        $this->expectException(Mage_Core_Exception::class);

        $args = $this->getBuilders();
        $args['service_builder'] = new stdClass();

        new Dhl_Versenden_Model_Webservice_Builder_Order($args);
    }

    /**
     * @test
     */
    public function constructorArgPackageBuilderMissing()
    {
        $this->expectException(Mage_Core_Exception::class);

        $args = $this->getBuilders();
        unset($args['package_builder']);

        new Dhl_Versenden_Model_Webservice_Builder_Order($args);
    }

    /**
     * @test
     */
    public function constructorArgPackageBuilderWrongType()
    {
        $this->expectException(Mage_Core_Exception::class);

        $args = $this->getBuilders();
        $args['package_builder'] = new stdClass();

        new Dhl_Versenden_Model_Webservice_Builder_Order($args);
    }

    /**
     * @test
     */
    public function constructorArgSettingsBuilderMissing()
    {
        $this->expectException(Mage_Core_Exception::class);

        $args = $this->getBuilders();
        unset($args['settings_builder']);

        new Dhl_Versenden_Model_Webservice_Builder_Order($args);
    }

    /**
     * @test
     */
    public function constructorArgSettingsBuilderWrongType()
    {
        $this->expectException(Mage_Core_Exception::class);

        $args = $this->getBuilders();
        $args['settings_builder'] = new stdClass();

        new Dhl_Versenden_Model_Webservice_Builder_Order($args);
    }

    /**
     * @test
     */
    public function buildPopulatesSdkBuilderWithShipmentData()
    {
        // Create mock SDK builder
        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Create minimal mock shipment
        $shipment = $this->getMockBuilder(Mage_Sales_Model_Order_Shipment::class)
            ->disableOriginalConstructor()
            ->setMethods(['getOrder', 'getShippingAddress', 'getStoreId'])
            ->getMock();

        $order = $this->getMockBuilder(Mage_Sales_Model_Order::class)
            ->disableOriginalConstructor()
            ->setMethods(['getIncrementId', 'getInvoiceCollection'])
            ->getMock();

        $invoice = $this->getMockBuilder(Mage_Sales_Model_Order_Invoice::class)
            ->disableOriginalConstructor()
            ->setMethods(['getIncrementId'])
            ->getMock();

        $invoiceCollection = $this->getMockBuilder(Mage_Sales_Model_Resource_Order_Invoice_Collection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getFirstItem'])
            ->getMock();

        $address = $this->getMockBuilder(Mage_Sales_Model_Order_Address::class)
            ->disableOriginalConstructor()
            ->getMock();

        $shipment->expects(static::any())->method('getOrder')->willReturn($order);
        $shipment->expects(static::any())->method('getShippingAddress')->willReturn($address);
        $shipment->expects(static::any())->method('getStoreId')->willReturn(1);
        $order->expects(static::any())->method('getIncrementId')->willReturn('100000001');
        $order->expects(static::any())->method('getInvoiceCollection')->willReturn($invoiceCollection);
        $invoiceCollection->expects(static::any())->method('getFirstItem')->willReturn($invoice);
        $invoice->expects(static::any())->method('getIncrementId')->willReturn('200000001');

        // Setup package info
        $packageInfo = [
            '1' => [
                'params' => [
                    'weight' => 1.5,
                    'length' => 30,
                    'width' => 20,
                    'height' => 10,
                ],
            ],
        ];

        $serviceInfo = [];
        $customsInfo = [];
        $gkApiProduct = 'V01PAK';

        // Get builders
        $args = $this->getBuilders();
        foreach (['shipper_builder', 'receiver_builder', 'service_builder', 'package_builder', 'customs_builder'] as $key) {
            $args[$key] = $this->getMockBuilder(get_class($args[$key]))
                ->disableOriginalConstructor()
                ->setMethods(['build'])
                ->getMock();
        }
        $args['info_builder'] = $this->getMockBuilder(Dhl_Versenden_Model_Info_Builder::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Create order builder
        $orderBuilder = new Dhl_Versenden_Model_Webservice_Builder_Order($args);

        // Test the build method - should not throw exception
        $result = $orderBuilder->build($sdkBuilder, $shipment, $packageInfo, $serviceInfo, $customsInfo, $gkApiProduct);

        // Verify void return
        static::assertNull($result);
    }

    /**
     * @test
     */
    public function buildCallsAllSubBuilders()
    {
        // Create mock SDK builder (use concrete class for compatibility with type hints)
        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['setShipmentDetails'])
            ->getMock();

        // Setup expectations for SDK builder methods
        $sdkBuilder->expects(static::once())
            ->method('setShipmentDetails')
            ->with(
                static::equalTo('V01PAK'),
                static::isInstanceOf(\DateTimeInterface::class),
                static::equalTo('100000001'),
            );

        // Create mock shipment with order
        $order = $this->getMockBuilder(Mage_Sales_Model_Order::class)
            ->disableOriginalConstructor()
            ->setMethods(['getIncrementId', 'getInvoiceCollection'])
            ->getMock();
        $order->expects(static::any())
            ->method('getIncrementId')
            ->willReturn('100000001');

        $address = $this->getMockBuilder(Mage_Sales_Model_Order_Address::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $shipment = $this->getMockBuilder(Mage_Sales_Model_Order_Shipment::class)
            ->disableOriginalConstructor()
            ->setMethods(['getOrder', 'getShippingAddress', 'getStoreId'])
            ->getMock();
        $shipment->expects(static::any())
            ->method('getOrder')
            ->willReturn($order);
        $shipment->expects(static::any())
            ->method('getShippingAddress')
            ->willReturn($address);
        $shipment->expects(static::any())
            ->method('getStoreId')
            ->willReturn(1);

        // Create mock invoice for customs
        $invoice = $this->getMockBuilder(Mage_Sales_Model_Order_Invoice::class)
            ->disableOriginalConstructor()
            ->setMethods(['getIncrementId'])
            ->getMock();
        $invoice->expects(static::any())
            ->method('getIncrementId')
            ->willReturn('200000001');

        $invoiceCollection = $this->getMockBuilder(Mage_Sales_Model_Resource_Order_Invoice_Collection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getFirstItem'])
            ->getMock();
        $invoiceCollection->expects(static::any())
            ->method('getFirstItem')
            ->willReturn($invoice);

        $order->expects(static::any())
            ->method('getInvoiceCollection')
            ->willReturn($invoiceCollection);

        // Setup package, service, and customs info
        $packageInfo = [
            '1' => [
                'params' => [
                    'weight' => 1.5,
                    'length' => 30,
                    'width' => 20,
                    'height' => 10,
                ],
            ],
        ];
        $serviceInfo = [];
        $customsInfo = [];
        $gkApiProduct = 'V01PAK';

        // Create mock sub-builders with expectations
        $shipperBuilder = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Builder_Shipper::class)
            ->disableOriginalConstructor()
            ->setMethods(['build'])
            ->getMock();
        $shipperBuilder->expects(static::once())
            ->method('build')
            ->with($sdkBuilder, 1, 'V01PAK', false);

        $receiverBuilder = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Builder_Receiver::class)
            ->disableOriginalConstructor()
            ->setMethods(['build'])
            ->getMock();
        $receiverBuilder->expects(static::once())
            ->method('build')
            ->with($sdkBuilder, $address);

        $serviceBuilder = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Builder_Service::class)
            ->disableOriginalConstructor()
            ->setMethods(['build'])
            ->getMock();
        $serviceBuilder->expects(static::once())
            ->method('build')
            ->with($sdkBuilder, $order, $serviceInfo);

        $packageBuilder = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Builder_Package::class)
            ->disableOriginalConstructor()
            ->setMethods(['build'])
            ->getMock();
        $packageBuilder->expects(static::once())
            ->method('build')
            ->with($sdkBuilder, $packageInfo);

        $customsBuilder = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Builder_Customs::class)
            ->disableOriginalConstructor()
            ->setMethods(['build'])
            ->getMock();
        $customsBuilder->expects(static::once())
            ->method('build')
            ->with($sdkBuilder, '200000001', $customsInfo, $packageInfo);

        $settingsBuilder = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Builder_Settings::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $infoBuilder = $this->getMockBuilder(Dhl_Versenden_Model_Info_Builder::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Create order builder with mock sub-builders
        $args = [
            'shipper_builder' => $shipperBuilder,
            'receiver_builder' => $receiverBuilder,
            'service_builder' => $serviceBuilder,
            'package_builder' => $packageBuilder,
            'customs_builder' => $customsBuilder,
            'settings_builder' => $settingsBuilder,
            'info_builder' => $infoBuilder,
        ];
        $orderBuilder = new Dhl_Versenden_Model_Webservice_Builder_Order($args);

        // Execute build method
        $orderBuilder->build($sdkBuilder, $shipment, $packageInfo, $serviceInfo, $customsInfo, $gkApiProduct);

        // Expectations verified automatically by PHPUnit
    }

    /**
     * @test
     */
    public function buildCallsSetShipmentOnShipperBuilder()
    {
        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Create mock shipment
        $order = $this->getMockBuilder(Mage_Sales_Model_Order::class)
            ->disableOriginalConstructor()
            ->setMethods(['getIncrementId', 'getInvoiceCollection'])
            ->getMock();
        $order->method('getIncrementId')->willReturn('100000001');

        $invoice = $this->getMockBuilder(Mage_Sales_Model_Order_Invoice::class)
            ->disableOriginalConstructor()
            ->setMethods(['getIncrementId'])
            ->getMock();
        $invoice->method('getIncrementId')->willReturn('200000001');

        $invoiceCollection = $this->getMockBuilder(Mage_Sales_Model_Resource_Order_Invoice_Collection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getFirstItem'])
            ->getMock();
        $invoiceCollection->method('getFirstItem')->willReturn($invoice);
        $order->method('getInvoiceCollection')->willReturn($invoiceCollection);

        $address = $this->getMockBuilder(Mage_Sales_Model_Order_Address::class)
            ->disableOriginalConstructor()
            ->getMock();

        $shipment = $this->getMockBuilder(Mage_Sales_Model_Order_Shipment::class)
            ->disableOriginalConstructor()
            ->setMethods(['getOrder', 'getShippingAddress', 'getStoreId'])
            ->getMock();
        $shipment->method('getOrder')->willReturn($order);
        $shipment->method('getShippingAddress')->willReturn($address);
        $shipment->method('getStoreId')->willReturn(1);

        // Create shipper builder mock - MUST expect setShipment called with the shipment
        $shipperBuilder = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Builder_Shipper::class)
            ->disableOriginalConstructor()
            ->setMethods(['setShipment', 'build'])
            ->getMock();

        $shipperBuilder->expects(static::once())
            ->method('setShipment')
            ->with($shipment)
            ->willReturnSelf();

        $shipperBuilder->expects(static::once())
            ->method('build');

        // Other builders as stubs
        $receiverBuilder = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Builder_Receiver::class)
            ->disableOriginalConstructor()
            ->setMethods(['build'])
            ->getMock();
        $serviceBuilder = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Builder_Service::class)
            ->disableOriginalConstructor()
            ->setMethods(['build'])
            ->getMock();
        $packageBuilder = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Builder_Package::class)
            ->disableOriginalConstructor()
            ->setMethods(['build'])
            ->getMock();
        $customsBuilder = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Builder_Customs::class)
            ->disableOriginalConstructor()
            ->setMethods(['build'])
            ->getMock();
        $settingsBuilder = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Builder_Settings::class)
            ->disableOriginalConstructor()
            ->getMock();
        $infoBuilder = $this->getMockBuilder(Dhl_Versenden_Model_Info_Builder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $args = [
            'shipper_builder' => $shipperBuilder,
            'receiver_builder' => $receiverBuilder,
            'service_builder' => $serviceBuilder,
            'package_builder' => $packageBuilder,
            'customs_builder' => $customsBuilder,
            'settings_builder' => $settingsBuilder,
            'info_builder' => $infoBuilder,
        ];
        $orderBuilder = new Dhl_Versenden_Model_Webservice_Builder_Order($args);

        $orderBuilder->build($sdkBuilder, $shipment, [], [], [], 'V01PAK');
    }

    /**
     * Create common test fixtures for shipment/order/invoice mocks.
     *
     * @return array{sdkBuilder: PHPUnit_Framework_MockObject_MockObject, shipment: PHPUnit_Framework_MockObject_MockObject, address: PHPUnit_Framework_MockObject_MockObject, order: PHPUnit_Framework_MockObject_MockObject}
     */
    protected function createShipmentFixtures()
    {
        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $order = $this->getMockBuilder(Mage_Sales_Model_Order::class)
            ->disableOriginalConstructor()
            ->setMethods(['getIncrementId', 'getInvoiceCollection'])
            ->getMock();
        $order->method('getIncrementId')->willReturn('100000001');

        $invoice = $this->getMockBuilder(Mage_Sales_Model_Order_Invoice::class)
            ->disableOriginalConstructor()
            ->setMethods(['getIncrementId'])
            ->getMock();
        $invoice->method('getIncrementId')->willReturn('200000001');

        $invoiceCollection = $this->getMockBuilder(Mage_Sales_Model_Resource_Order_Invoice_Collection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getFirstItem'])
            ->getMock();
        $invoiceCollection->method('getFirstItem')->willReturn($invoice);
        $order->method('getInvoiceCollection')->willReturn($invoiceCollection);

        $address = $this->getMockBuilder(Mage_Sales_Model_Order_Address::class)
            ->disableOriginalConstructor()
            ->getMock();

        $shipment = $this->getMockBuilder(Mage_Sales_Model_Order_Shipment::class)
            ->disableOriginalConstructor()
            ->setMethods(['getOrder', 'getShippingAddress', 'getStoreId'])
            ->getMock();
        $shipment->method('getOrder')->willReturn($order);
        $shipment->method('getShippingAddress')->willReturn($address);
        $shipment->method('getStoreId')->willReturn(1);

        return [
            'sdkBuilder' => $sdkBuilder,
            'shipment' => $shipment,
            'address' => $address,
            'order' => $order,
        ];
    }

    /**
     * Create stub sub-builders (all except receiver, which the caller configures).
     *
     * @return array
     */
    protected function createStubBuilders()
    {
        $shipperBuilder = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Builder_Shipper::class)
            ->disableOriginalConstructor()
            ->setMethods(['setShipment', 'build'])
            ->getMock();
        $shipperBuilder->method('setShipment')->willReturnSelf();

        return [
            'shipper_builder' => $shipperBuilder,
            'service_builder' => $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Builder_Service::class)
                ->disableOriginalConstructor()->setMethods(['build'])->getMock(),
            'package_builder' => $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Builder_Package::class)
                ->disableOriginalConstructor()->setMethods(['build'])->getMock(),
            'customs_builder' => $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Builder_Customs::class)
                ->disableOriginalConstructor()->setMethods(['build'])->getMock(),
            'settings_builder' => $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Builder_Settings::class)
                ->disableOriginalConstructor()->getMock(),
            'info_builder' => $this->getMockBuilder(Dhl_Versenden_Model_Info_Builder::class)
                ->disableOriginalConstructor()->getMock(),
        ];
    }

    /**
     * When parcelAnnouncement is enabled in serviceInfo, receiver builder
     * should be called with includeRecipientEmail=true.
     *
     * @test
     */
    public function buildPassesIncludeEmailTrueWhenParcelAnnouncementEnabled()
    {
        $fixtures = $this->createShipmentFixtures();
        $serviceInfo = [
            'shipment_service' => [
                \Dhl\Versenden\ParcelDe\Service\ParcelAnnouncement::CODE => '1',
            ],
        ];

        $receiverBuilder = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Builder_Receiver::class)
            ->disableOriginalConstructor()
            ->setMethods(['build'])
            ->getMock();
        $receiverBuilder->expects(static::once())
            ->method('build')
            ->with($fixtures['sdkBuilder'], $fixtures['address'], true);

        $args = $this->createStubBuilders();
        $args['receiver_builder'] = $receiverBuilder;
        $orderBuilder = new Dhl_Versenden_Model_Webservice_Builder_Order($args);

        $orderBuilder->build(
            $fixtures['sdkBuilder'],
            $fixtures['shipment'],
            [],
            $serviceInfo,
            [],
            'V01PAK',
        );
    }

    /**
     * When returnShipment is enabled in serviceInfo, shipper builder
     * should be called with includeReturnShipment=true.
     *
     * @test
     */
    public function buildPassesReturnShipmentFlagToShipperBuilder()
    {
        $fixtures = $this->createShipmentFixtures();
        $serviceInfo = [
            'shipment_service' => [
                \Dhl\Versenden\ParcelDe\Service\ReturnShipment::CODE => '1',
            ],
        ];

        $shipperBuilder = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Builder_Shipper::class)
            ->disableOriginalConstructor()
            ->setMethods(['setShipment', 'build'])
            ->getMock();
        $shipperBuilder->method('setShipment')->willReturnSelf();
        $shipperBuilder->expects(static::once())
            ->method('build')
            ->with($fixtures['sdkBuilder'], 1, 'V01PAK', true);

        $receiverBuilder = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Builder_Receiver::class)
            ->disableOriginalConstructor()
            ->setMethods(['build'])
            ->getMock();

        $args = $this->createStubBuilders();
        $args['shipper_builder'] = $shipperBuilder;
        $args['receiver_builder'] = $receiverBuilder;
        $orderBuilder = new Dhl_Versenden_Model_Webservice_Builder_Order($args);

        $orderBuilder->build(
            $fixtures['sdkBuilder'],
            $fixtures['shipment'],
            [],
            $serviceInfo,
            [],
            'V01PAK',
        );
    }

    /**
     * When closestDropPoint is enabled in serviceInfo (autocreate path),
     * receiver builder should be called with includeRecipientEmail=true
     * because CDP delivery requires email for drop-point notifications.
     *
     * @test
     */
    public function buildPassesIncludeEmailTrueWhenClosestDropPointEnabled()
    {
        $fixtures = $this->createShipmentFixtures();
        $serviceInfo = [
            'shipment_service' => [
                \Dhl\Versenden\ParcelDe\Service\ClosestDropPoint::CODE => '1',
            ],
        ];

        $receiverBuilder = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Builder_Receiver::class)
            ->disableOriginalConstructor()
            ->setMethods(['build'])
            ->getMock();
        $receiverBuilder->expects(static::once())
            ->method('build')
            ->with($fixtures['sdkBuilder'], $fixtures['address'], true);

        $args = $this->createStubBuilders();
        $args['receiver_builder'] = $receiverBuilder;
        $orderBuilder = new Dhl_Versenden_Model_Webservice_Builder_Order($args);

        $orderBuilder->build(
            $fixtures['sdkBuilder'],
            $fixtures['shipment'],
            [],
            $serviceInfo,
            [],
            'V01PAK',
        );
    }

    /**
     * When DeliveryType is set to CDP in service_setting (packaging popup path),
     * receiver builder should be called with includeRecipientEmail=true
     * because CDP delivery requires email for drop-point notifications.
     *
     * @test
     */
    public function buildPassesIncludeEmailTrueWhenDeliveryTypeCdp()
    {
        $fixtures = $this->createShipmentFixtures();
        $serviceInfo = [
            'shipment_service' => [
                \Dhl\Versenden\ParcelDe\Service\DeliveryType::CODE => '1',
            ],
            'service_setting' => [
                \Dhl\Versenden\ParcelDe\Service\DeliveryType::CODE => \Dhl\Versenden\ParcelDe\Service\DeliveryType::CDP,
            ],
        ];

        $receiverBuilder = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Builder_Receiver::class)
            ->disableOriginalConstructor()
            ->setMethods(['build'])
            ->getMock();
        $receiverBuilder->expects(static::once())
            ->method('build')
            ->with($fixtures['sdkBuilder'], $fixtures['address'], true);

        $args = $this->createStubBuilders();
        $args['receiver_builder'] = $receiverBuilder;
        $orderBuilder = new Dhl_Versenden_Model_Webservice_Builder_Order($args);

        $orderBuilder->build(
            $fixtures['sdkBuilder'],
            $fixtures['shipment'],
            [],
            $serviceInfo,
            [],
            'V53WPAK',
        );
    }

    /**
     * When parcelAnnouncement is not in serviceInfo, receiver builder
     * should be called with includeRecipientEmail=false.
     *
     * @test
     */
    public function buildPassesIncludeEmailFalseWhenParcelAnnouncementDisabled()
    {
        $fixtures = $this->createShipmentFixtures();
        $serviceInfo = [];

        $receiverBuilder = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Builder_Receiver::class)
            ->disableOriginalConstructor()
            ->setMethods(['build'])
            ->getMock();
        $receiverBuilder->expects(static::once())
            ->method('build')
            ->with($fixtures['sdkBuilder'], $fixtures['address'], false);

        $args = $this->createStubBuilders();
        $args['receiver_builder'] = $receiverBuilder;
        $orderBuilder = new Dhl_Versenden_Model_Webservice_Builder_Order($args);

        $orderBuilder->build(
            $fixtures['sdkBuilder'],
            $fixtures['shipment'],
            [],
            $serviceInfo,
            [],
            'V01PAK',
        );
    }
}
