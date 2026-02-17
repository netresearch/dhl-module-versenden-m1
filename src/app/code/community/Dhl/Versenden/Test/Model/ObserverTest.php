<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Test_Model_ObserverTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     * @loadFixture Model_ObserverTest
     */
    public function deleteShippingLabelUsesRestClient()
    {
        // Mock REST client
        $shipmentNumber = '00340434161094015902';

        $clientMock = $this->getModelMock(
            'dhl_versenden/webservice_client_shipment',
            ['cancelShipments'],
        );
        $clientMock->expects(static::once())
            ->method('cancelShipments')
            ->with(static::equalTo([$shipmentNumber]))
            ->willReturn([$shipmentNumber]);

        $this->replaceByMock('model', 'dhl_versenden/webservice_client_shipment', $clientMock);

        // Load shipment from fixture (has shipping label)
        $shipment = Mage::getModel('sales/order_shipment')->load(100);
        static::assertNotEmpty($shipment->getData('shipping_label'));

        // Setup track with DHL carrier
        $track = Mage::getModel('sales/order_shipment_track');
        $track->setCarrierCode(Dhl_Versenden_Model_Shipping_Carrier_Versenden::CODE);
        $track->setData('track_number', $shipmentNumber);
        $track->setShipment($shipment);

        $eventObserver = new Varien_Event_Observer();
        $eventObserver->setData('track', $track);

        // Execute
        $observer = new Dhl_Versenden_Model_Observer();
        $observer->deleteShippingLabel($eventObserver);

        // Verify label was removed
        static::assertEmpty($shipment->getData('shipping_label'));
    }

    /**
     * @test
     * @loadFixture Model_ObserverTest
     */
    public function deleteShippingLabelHandlesDetailedServiceException()
    {
        $this->expectException('Mage_Core_Exception');
        $this->expectExceptionMessageMatches('/Shipment not found|Unknown shipment number/');

        // Mock REST client to throw DetailedServiceException
        $clientMock = $this->getModelMock(
            'dhl_versenden/webservice_client_shipment',
            ['cancelShipments'],
        );
        $clientMock->expects(static::once())
            ->method('cancelShipments')
            ->willThrowException(
                new \Dhl\Sdk\ParcelDe\Shipping\Exception\DetailedServiceException('Shipment not found'),
            );

        $this->replaceByMock('model', 'dhl_versenden/webservice_client_shipment', $clientMock);

        // Load shipment from fixture (has shipping label)
        $shipment = Mage::getModel('sales/order_shipment')->load(100);

        // Setup track with DHL carrier
        $track = Mage::getModel('sales/order_shipment_track');
        $track->setCarrierCode(Dhl_Versenden_Model_Shipping_Carrier_Versenden::CODE);
        $track->setData('track_number', 'INVALID_NUMBER');
        $track->setShipment($shipment);

        $eventObserver = new Varien_Event_Observer();
        $eventObserver->setData('track', $track);

        // Execute - should throw exception
        $observer = new Dhl_Versenden_Model_Observer();
        $observer->deleteShippingLabel($eventObserver);
    }

    /**
     * @test
     * @loadFixture Model_ObserverTest
     */
    public function deleteShippingLabelHandlesServiceException()
    {
        $this->expectException('Mage_Core_Exception');
        $this->expectExceptionMessageMatches('/Shipment deletion failed|canceled/');

        // Mock REST client to throw generic ServiceException
        $clientMock = $this->getModelMock(
            'dhl_versenden/webservice_client_shipment',
            ['cancelShipments'],
        );
        $clientMock->expects(static::once())
            ->method('cancelShipments')
            ->willThrowException(
                new \Dhl\Sdk\ParcelDe\Shipping\Exception\ServiceException('API error'),
            );

        $this->replaceByMock('model', 'dhl_versenden/webservice_client_shipment', $clientMock);

        // Load shipment from fixture (has shipping label)
        $shipment = Mage::getModel('sales/order_shipment')->load(100);

        // Setup track with DHL carrier
        $track = Mage::getModel('sales/order_shipment_track');
        $track->setCarrierCode(Dhl_Versenden_Model_Shipping_Carrier_Versenden::CODE);
        $track->setData('track_number', '00340434161094015902');
        $track->setShipment($shipment);

        $eventObserver = new Varien_Event_Observer();
        $eventObserver->setData('track', $track);

        // Execute - should throw exception
        $observer = new Dhl_Versenden_Model_Observer();
        $observer->deleteShippingLabel($eventObserver);
    }

    /**
     * @test
     */
    public function deleteShippingLabelSkipsNonDhlCarrier()
    {
        // Mock REST client - should NOT be called
        $clientMock = $this->getModelMock(
            'dhl_versenden/webservice_client_shipment',
            ['cancelShipments'],
        );
        $clientMock->expects(static::never())
            ->method('cancelShipments');

        $this->replaceByMock('model', 'dhl_versenden/webservice_client_shipment', $clientMock);

        // Setup track with non-DHL carrier
        $track = Mage::getModel('sales/order_shipment_track');
        $track->setCarrierCode('ups'); // Non-DHL carrier

        $eventObserver = new Varien_Event_Observer();
        $eventObserver->setData('track', $track);

        // Execute - should return early without calling REST client
        $observer = new Dhl_Versenden_Model_Observer();
        $observer->deleteShippingLabel($eventObserver);
    }

    /**
     * @test
     * @loadFixture Model_ObserverTest
     */
    public function deleteShippingLabelSkipsShipmentWithoutLabel()
    {
        // Mock REST client - should NOT be called
        $clientMock = $this->getModelMock(
            'dhl_versenden/webservice_client_shipment',
            ['cancelShipments'],
        );
        $clientMock->expects(static::never())
            ->method('cancelShipments');

        $this->replaceByMock('model', 'dhl_versenden/webservice_client_shipment', $clientMock);

        // Load shipment from fixture (has NO shipping label)
        $shipment = Mage::getModel('sales/order_shipment')->load(110);
        static::assertFalse($shipment->hasShippingLabel());

        // Setup track with DHL carrier
        $track = Mage::getModel('sales/order_shipment_track');
        $track->setCarrierCode(Dhl_Versenden_Model_Shipping_Carrier_Versenden::CODE);
        $track->setShipment($shipment);

        $eventObserver = new Varien_Event_Observer();
        $eventObserver->setData('track', $track);

        // Execute - should return early without calling REST client
        $observer = new Dhl_Versenden_Model_Observer();
        $observer->deleteShippingLabel($eventObserver);
    }

    /**
     * Test setShippingServiceFlags sets flags when both services enabled.
     *
     * @test
     */
    public function setShippingServiceFlagsSetsAllEnabledFlag()
    {
        // Mock helper to return both services enabled
        $helperMock = $this->getHelperMock('dhl_versenden/service', [
            'isLocationAndNeighbourEnabled',
            'isLocationOrNeighbourEnabled',
        ]);
        $helperMock->method('isLocationAndNeighbourEnabled')->willReturn(true);
        $helperMock->method('isLocationOrNeighbourEnabled')->willReturn(true);
        $this->replaceByMock('helper', 'dhl_versenden/service', $helperMock);

        // Create head block in layout
        $headBlock = Mage::app()->getLayout()->createBlock('page/html_head', 'head');
        Mage::app()->getLayout()->setBlock('head', $headBlock);

        $observer = new Dhl_Versenden_Model_Observer();
        $observer->setShippingServiceFlags();

        static::assertTrue(
            $headBlock->getData(Dhl_Versenden_Helper_Service::PREFERRED_SERVICE_ALL_ENABLED),
        );
        static::assertTrue(
            $headBlock->getData(Dhl_Versenden_Helper_Service::PREFERRED_SERVICE_ANY_ENABLED),
        );
    }

    /**
     * Test setShippingServiceFlags only sets ANY flag when only one service enabled.
     *
     * @test
     */
    public function setShippingServiceFlagsSetsOnlyAnyEnabledFlag()
    {
        // Mock helper to return only one service enabled
        $helperMock = $this->getHelperMock('dhl_versenden/service', [
            'isLocationAndNeighbourEnabled',
            'isLocationOrNeighbourEnabled',
        ]);
        $helperMock->method('isLocationAndNeighbourEnabled')->willReturn(false);
        $helperMock->method('isLocationOrNeighbourEnabled')->willReturn(true);
        $this->replaceByMock('helper', 'dhl_versenden/service', $helperMock);

        // Create head block in layout
        $headBlock = Mage::app()->getLayout()->createBlock('page/html_head', 'head');
        Mage::app()->getLayout()->setBlock('head', $headBlock);

        $observer = new Dhl_Versenden_Model_Observer();
        $observer->setShippingServiceFlags();

        static::assertNull(
            $headBlock->getData(Dhl_Versenden_Helper_Service::PREFERRED_SERVICE_ALL_ENABLED),
        );
        static::assertTrue(
            $headBlock->getData(Dhl_Versenden_Helper_Service::PREFERRED_SERVICE_ANY_ENABLED),
        );
    }

    /**
     * Test addServiceFee returns early when non-DHL shipping method.
     *
     * @test
     */
    public function addServiceFeeReturnsEarlyForNonDhlMethod()
    {
        // Mock checkout session to avoid session_save_path errors
        $sessionMock = $this->getModelMock('checkout/session', ['init', 'getQuote']);
        $this->replaceByMock('singleton', 'checkout/session', $sessionMock);

        // Create quote with non-DHL shipping method
        $shippingAddress = Mage::getModel('sales/quote_address');
        $shippingAddress->setShippingMethod('flatrate_flatrate');

        $quote = Mage::getModel('sales/quote');
        $quote->setShippingAddress($shippingAddress);

        $eventObserver = new Varien_Event_Observer();
        $eventObserver->setData('quote', $quote);

        $observer = new Dhl_Versenden_Model_Observer();
        $observer->addServiceFee($eventObserver);

        // Verify no versenden info remains
        static::assertNull($shippingAddress->getData('dhl_versenden_info'));
    }

    /**
     * Test addServiceFee returns early when no versenden info.
     *
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function addServiceFeeReturnsEarlyWhenNoVersendenInfo()
    {
        $this->setCurrentStore('store_two');

        // Mock checkout session to avoid session_save_path errors
        $sessionMock = $this->getModelMock('checkout/session', ['init', 'getQuote']);
        $this->replaceByMock('singleton', 'checkout/session', $sessionMock);

        // Create quote with DHL shipping method but no versenden info
        $shippingAddress = Mage::getModel('sales/quote_address');
        $shippingAddress->setShippingMethod('dhlversenden_flatrate');
        $shippingAddress->setData('dhl_versenden_info', null);

        $quote = Mage::getModel('sales/quote');
        $quote->setStoreId(Mage::app()->getStore()->getId());
        $quote->setShippingAddress($shippingAddress);

        $eventObserver = new Varien_Event_Observer();
        $eventObserver->setData('quote', $quote);

        $observer = new Dhl_Versenden_Model_Observer();
        $observer->addServiceFee($eventObserver);

        // Should complete without error (no fee added)
        static::assertTrue(true);
    }

    /**
     * Test addServiceFee adds handling fee for preferred day service.
     *
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function addServiceFeeAddsHandlingFeeForPreferredDay()
    {
        $this->setCurrentStore('store_two');

        // Mock checkout session to avoid session_save_path errors
        $sessionMock = $this->getModelMock('checkout/session', ['init', 'getQuote']);
        $this->replaceByMock('singleton', 'checkout/session', $sessionMock);

        // Mock config_shipment to allow processing
        $configMock = $this->getModelMock('dhl_versenden/config_shipment', ['canProcessMethod']);
        $configMock->method('canProcessMethod')->willReturn(true);
        $this->replaceByMock('model', 'dhl_versenden/config_shipment', $configMock);

        // Mock config_service for fee calculation
        $serviceConfigMock = $this->getModelMock('dhl_versenden/config_service', ['getPrefDayFee']);
        $serviceConfigMock->method('getPrefDayFee')->willReturn(1.99);
        $this->replaceByMock('model', 'dhl_versenden/config_service', $serviceConfigMock);

        // Create versenden info with preferred day selected
        $versendenInfo = new \Dhl\Versenden\ParcelDe\Info();
        $versendenInfo->getServices()->preferredDay = '2025-12-01';

        // Create shipping address with versenden info
        $shippingAddress = Mage::getModel('sales/quote_address');
        $shippingAddress->setShippingMethod('dhlversenden_flatrate');
        $shippingAddress->setData('dhl_versenden_info', $versendenInfo);

        // Mock quote to return our shipping address consistently
        $quoteMock = $this->getMockBuilder(Mage_Sales_Model_Quote::class)
            ->setMethods(['getShippingAddress', 'getStoreId'])
            ->getMock();
        $quoteMock->method('getShippingAddress')->willReturn($shippingAddress);
        $quoteMock->method('getStoreId')->willReturn(Mage::app()->getStore()->getId());

        $eventObserver = new Varien_Event_Observer();
        $eventObserver->setData('quote', $quoteMock);

        $observer = new Dhl_Versenden_Model_Observer();
        $observer->addServiceFee($eventObserver);

        // Verify collect shipping rates flag is set
        static::assertTrue($shippingAddress->getCollectShippingRates());
    }

    /**
     * Test addAutocreateMassAction adds action to sales order grid.
     *
     * @test
     */
    public function addAutocreateMassActionAddsToOrderGrid()
    {
        // Mock request
        $requestMock = $this->getMockBuilder(Mage_Core_Controller_Request_Http::class)
            ->setMethods(['getControllerName'])
            ->getMock();
        $requestMock->method('getControllerName')->willReturn('sales_order');

        // Mock massaction block
        $blockMock = $this->getMockBuilder(Mage_Adminhtml_Block_Widget_Grid_Massaction::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRequest', 'addItem', 'getUrl'])
            ->getMock();
        $blockMock->method('getRequest')->willReturn($requestMock);
        $blockMock->method('getUrl')->willReturn('http://example.com/massaction');
        $blockMock->expects(static::once())
            ->method('addItem')
            ->with(
                'dhlversenden_label_create',
                static::callback(function ($itemData) {
                    return isset($itemData['label']) && isset($itemData['url']);
                }),
            );

        $event = new Varien_Event();
        $event->setData('block', $blockMock);

        $eventObserver = new Varien_Event_Observer();
        $eventObserver->setEvent($event);

        $observer = new Dhl_Versenden_Model_Observer();
        $observer->addAutocreateMassAction($eventObserver);
    }

    /**
     * Test addAutocreateMassAction ignores non-order grid.
     *
     * @test
     */
    public function addAutocreateMassActionIgnoresNonOrderGrid()
    {
        // Mock request for different controller
        $requestMock = $this->getMockBuilder(Mage_Core_Controller_Request_Http::class)
            ->setMethods(['getControllerName'])
            ->getMock();
        $requestMock->method('getControllerName')->willReturn('catalog_product');

        // Mock massaction block
        $blockMock = $this->getMockBuilder(Mage_Adminhtml_Block_Widget_Grid_Massaction::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRequest', 'addItem'])
            ->getMock();
        $blockMock->method('getRequest')->willReturn($requestMock);
        $blockMock->expects(static::never())
            ->method('addItem');

        $event = new Varien_Event();
        $event->setData('block', $blockMock);

        $eventObserver = new Varien_Event_Observer();
        $eventObserver->setEvent($event);

        $observer = new Dhl_Versenden_Model_Observer();
        $observer->addAutocreateMassAction($eventObserver);
    }

    /**
     * Test addAutocreateMassAction ignores non-massaction blocks.
     *
     * @test
     */
    public function addAutocreateMassActionIgnoresNonMassactionBlocks()
    {
        $block = Mage::app()->getLayout()->createBlock('core/text');

        $event = new Varien_Event();
        $event->setData('block', $block);

        $eventObserver = new Varien_Event_Observer();
        $eventObserver->setEvent($event);

        // Should complete without error
        $observer = new Dhl_Versenden_Model_Observer();
        $observer->addAutocreateMassAction($eventObserver);

        static::assertTrue(true);
    }
}
