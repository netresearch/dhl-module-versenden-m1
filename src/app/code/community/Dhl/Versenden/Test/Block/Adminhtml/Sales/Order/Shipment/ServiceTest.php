<?php

/**
 * See LICENSE.md for license details.
 */

use Dhl\Versenden\ParcelDe\Service;

class Dhl_Versenden_Test_Block_Adminhtml_Sales_Order_Shipment_ServiceTest extends EcomDev_PHPUnit_Test_Case
{
    public const EDIT_BLOCK_ALIAS = 'dhl_versenden/adminhtml_sales_order_shipment_service_edit';
    public const VIEW_BLOCK_ALIAS = 'dhl_versenden/adminhtml_sales_order_shipment_service_view';

    protected function mockEditBlock()
    {
        $editBlockMock = $this->getBlockMock(self::EDIT_BLOCK_ALIAS, ['fetchView']);
        $this->replaceByMock('block', self::EDIT_BLOCK_ALIAS, $editBlockMock);
    }

    protected function mockViewBlock()
    {
        $viewBlockMock = $this->getBlockMock(self::VIEW_BLOCK_ALIAS, []);
        $this->replaceByMock('block', self::VIEW_BLOCK_ALIAS, $viewBlockMock);
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Create shipment with order relationship
        $shippingAddress = Mage::getModel('sales/order_address');
        $shippingAddress->setCountryId('DE');

        $order = Mage::getModel('sales/order');
        $order->setShippingAddress($shippingAddress);
        $order->setShippingMethod('dhlversenden_flatrate');

        $shipment = Mage::getModel('sales/order_shipment');
        $shipment->setStoreId(1);
        $shipment->setOrder($order);

        // CRITICAL: Register BEFORE creating block mocks
        Mage::register('current_shipment', $shipment);

        // Mock service config to return properly populated service collection
        // This is required because Edit.php::getServices() calls serviceConfig->getAvailableServices()
        $services = [
            new Service\BulkyGoods('', true, false),
            new Service\PreferredLocation('', true, false, ''),
            new Service\PreferredNeighbour('', true, false, ''),
            new Service\PreferredDay('', true, false, ''),
            new Service\ParcelAnnouncement('', true, false),
            new Service\VisualCheckOfAge('', true, false, ''),
            new Service\ReturnShipment('', true, false),
            new Service\AdditionalInsurance('', true, false, ''),
            new Service\Cod('', true, false, ''),
            new Service\ParcelOutletRouting('', true, false, ''),
            new Service\ClosestDropPoint('Closest Drop Point', true, false),
            new Service\DeliveryType('Delivery Type', true, false, [
                Service\DeliveryType::ECONOMY => 'Economy',
                Service\DeliveryType::PREMIUM => 'Premium',
                Service\DeliveryType::CDP => 'Closest Drop Point',
            ]),
        ];
        $serviceCollection = new Service\Collection($services);

        $serviceConfigMock = $this->getModelMock('dhl_versenden/config_service', ['getAvailableServices']);
        $serviceConfigMock
            ->expects(static::any())
            ->method('getAvailableServices')
            ->willReturn($serviceCollection);
        $this->replaceByMock('model', 'dhl_versenden/config_service', $serviceConfigMock);

        // Now create mocks - constructor can safely access registry
        $this->mockEditBlock();
        $this->mockViewBlock();
    }

    protected function tearDown(): void
    {
        // Clean up registry
        if (Mage::registry('current_shipment')) {
            Mage::unregister('current_shipment');
        }
        parent::tearDown();
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function renderViewWrongShippingMethod()
    {
        /** @var EcomDev_PHPUnit_Mock_Proxy|Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Service_Edit $block */
        $block = Mage::app()->getLayout()->createBlock(self::EDIT_BLOCK_ALIAS);
        $block->getShipment()->getOrder()->setShippingMethod('flatrate_flatrate');
        $block
            ->expects(static::never())
            ->method('fetchView');

        /** @var Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Service_Edit $block */
        static::assertEmpty($block->renderView());
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function renderViewDhlShippingMethod()
    {
        $blockHtml = 'foo';

        /** @var EcomDev_PHPUnit_Mock_Proxy|Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Service_Edit $block */
        $block = Mage::app()->getLayout()->createBlock(self::EDIT_BLOCK_ALIAS);
        $block->getShipment()->getOrder()->setShippingMethod('dhlversenden_flatrate');
        $block
            ->expects(static::exactly(1))
            ->method('fetchView')
            ->willReturn($blockHtml);

        static::assertEquals($blockHtml, $block->renderView());
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function selectedServicesForEdit()
    {
        $preferredLocation = 'Garage';

        $info = new \Dhl\Versenden\ParcelDe\Info();
        $info->getServices()->bulkyGoods = true;
        $info->getServices()->preferredLocation = $preferredLocation;

        /** @var EcomDev_PHPUnit_Mock_Proxy|Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Service_Edit $block */
        $block = Mage::app()->getLayout()->createBlock(self::EDIT_BLOCK_ALIAS);
        $block->getShipment()->getOrder()->setShippingMethod('dhlversenden_flatrate');
        $block->getShipment()->getOrder()->getShippingAddress()->setData('dhl_versenden_info', $info);

        /** @var \Dhl\Versenden\ParcelDe\Service\Collection $services */
        $services = $block->getServices();
        static::assertInstanceOf('\Dhl\Versenden\ParcelDe\Service\Collection', $services);
        static::assertContainsOnly('\Dhl\Versenden\ParcelDe\Service\Type\Generic', $services);

        // bulkyGoods disabled via config
        $code = \Dhl\Versenden\ParcelDe\Service\BulkyGoods::CODE;
        static::assertTrue($services->getItem($code)->isEnabled());

        // preferredLocation enabled via config and preselected via dhl_versenden_info
        $code = \Dhl\Versenden\ParcelDe\Service\PreferredLocation::CODE;
        static::assertEquals('Garage', $services->getItem($code)->getValue());
    }

    /**
     * @test
     */
    public function allServicesForEdit()
    {
        /** @var EcomDev_PHPUnit_Mock_Proxy|Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Service_Edit $block */
        $block = Mage::app()->getLayout()->createBlock(self::EDIT_BLOCK_ALIAS);
        $block->getShipment()->getOrder()->setShippingMethod('dhlversenden_flatrate');

        /** @var \Dhl\Versenden\ParcelDe\Service\Collection $services */
        $services = $block->getServices();
        static::assertInstanceOf('\Dhl\Versenden\ParcelDe\Service\Collection', $services);
        static::assertContainsOnly('\Dhl\Versenden\ParcelDe\Service\Type\Generic', $services);

        /** @var Dhl\Versenden\ParcelDe\Service\Type\Generic $service */
        foreach ($services as $service) {
            if ($service->getCode() === \Dhl\Versenden\ParcelDe\Service\ParcelAnnouncement::CODE) {
                // ParcelAnnouncement is auto-selected for non-customer service (Edit.php:123)
                static::assertTrue($service->isSelected());
            } else {
                static::assertFalse($service->isSelected());
            }
        }

        // COD is removed for orders without COD payment method
        static::assertNull(
            $services->getItem(Service\Cod::CODE),
            'COD must not appear in packaging popup for non-COD orders'
        );
    }

    /**
     * COD must be auto-selected and present when the order uses a COD payment method.
     *
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function codServiceSelectedForCodPayment()
    {
        Mage::app()->getStore()->setConfig(
            'carriers/dhlversenden/shipment_dhlcodmethods',
            'cashondelivery'
        );

        $payment = Mage::getModel('sales/order_payment');
        $payment->setMethod('cashondelivery');

        /** @var EcomDev_PHPUnit_Mock_Proxy|Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Service_Edit $block */
        $block = Mage::app()->getLayout()->createBlock(self::EDIT_BLOCK_ALIAS);
        $block->getShipment()->getOrder()->setShippingMethod('dhlversenden_flatrate');
        $block->getShipment()->getOrder()->setPayment($payment);

        /** @var \Dhl\Versenden\ParcelDe\Service\Collection $services */
        $services = $block->getServices();

        $codService = $services->getItem(Service\Cod::CODE);
        static::assertNotNull($codService, 'COD must appear in packaging popup for COD payment orders');
        static::assertTrue($codService->isSelected(), 'COD must be auto-selected for COD payment orders');
    }

    /**
     * COD must be rendered read-only in the packaging popup since it is
     * determined by the payment method, not by admin choice.
     *
     * @test
     */
    public function codRendererIsReadOnly()
    {
        $block = Mage::app()->getLayout()->createBlock(self::EDIT_BLOCK_ALIAS);

        $codService = new Service\Cod('Cash on Delivery', true, false, '');
        $codService->setValue('1');
        $codService->setDefaultValue('');

        $renderer = $block->getRenderer($codService);
        // Read-only renderer shows a disabled, checked, locked checkbox with name/value
        $selectorHtml = $renderer->getSelectorHtml();
        static::assertStringContainsString('disabled="disabled"', $selectorHtml);
        static::assertStringContainsString('checked="checked"', $selectorHtml);
        static::assertStringContainsString('data-locked="1"', $selectorHtml);
        static::assertStringContainsString('id="shipment_service_cod"', $selectorHtml);
        static::assertStringContainsString('name="shipment_service[cod]"', $selectorHtml);
        static::assertStringContainsString('value="cod"', $selectorHtml);
        static::assertEmpty($renderer->getValueHtml(), 'Read-only renderer must not show value text');
    }

    /**
     * ParcelAnnouncement must be rendered read-only in the packaging popup,
     * matching M2 where it has disabled=true in shipping_settings.xml.
     *
     * @test
     */
    public function parcelAnnouncementRendererIsReadOnly()
    {
        $block = Mage::app()->getLayout()->createBlock(self::EDIT_BLOCK_ALIAS);

        $paService = new Service\ParcelAnnouncement('Parcel Announcement', true, false);
        $paService->setValue('1');

        $renderer = $block->getRenderer($paService);
        static::assertTrue($renderer->isReadOnly());

        $selectorHtml = $renderer->getSelectorHtml();
        static::assertStringContainsString('disabled="disabled"', $selectorHtml);
        static::assertStringContainsString('checked="checked"', $selectorHtml);
        static::assertStringContainsString('data-locked="1"', $selectorHtml);
        static::assertStringContainsString('id="shipment_service_parcelAnnouncement"', $selectorHtml);
        static::assertStringContainsString('name="shipment_service[parcelAnnouncement]"', $selectorHtml);
        static::assertStringContainsString('value="parcelAnnouncement"', $selectorHtml);
        static::assertEmpty($renderer->getValueHtml(), 'Read-only renderer must not show value text');
    }

    /**
     * Non-COD services must still use an editable renderer.
     *
     * @test
     */
    public function getRendererForEdit()
    {
        $block = Mage::app()->getLayout()->createBlock('dhl_versenden/adminhtml_sales_order_shipment_service_edit');

        $location = 'Melmac';
        $service = new \Dhl\Versenden\ParcelDe\Service\PreferredLocation('', true, true, '');
        $service->setValue($location);

        $renderer = $block->getRenderer($service);
        static::assertNotEquals($location, $renderer->getValueHtml());
    }

    // =========================================================================
    // ClosestDropPoint → DeliveryType CDP locking
    // =========================================================================

    /**
     * When customer selected ClosestDropPoint during checkout, the admin
     * packaging popup must lock DeliveryType to CDP only and remove
     * ClosestDropPoint from the visible services.
     *
     * @test
     */
    public function closestDropPointLocksDeliveryTypeToCdp()
    {
        $info = new \Dhl\Versenden\ParcelDe\Info();
        $info->getServices()->closestDropPoint = true;

        /** @var Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Service_Edit $block */
        $block = Mage::app()->getLayout()->createBlock(self::EDIT_BLOCK_ALIAS);
        $block->getShipment()->getOrder()->setShippingMethod('dhlversenden_flatrate');
        $block->getShipment()->getOrder()->getShippingAddress()->setData('dhl_versenden_info', $info);

        /** @var \Dhl\Versenden\ParcelDe\Service\Collection $services */
        $services = $block->getServices();

        // ClosestDropPoint must be removed from admin
        static::assertNull(
            $services->getItem(Service\ClosestDropPoint::CODE),
            'ClosestDropPoint must not appear in admin packaging popup'
        );

        // DeliveryType must be locked to CDP only
        $deliveryType = $services->getItem(Service\DeliveryType::CODE);
        static::assertNotNull($deliveryType, 'DeliveryType must remain in admin packaging popup');
        static::assertCount(1, $deliveryType->getOptions(), 'DeliveryType must have only CDP option');
        static::assertArrayHasKey(
            Service\DeliveryType::CDP,
            $deliveryType->getOptions(),
            'DeliveryType must contain CDP option'
        );
        static::assertEquals(
            Service\DeliveryType::CDP,
            $deliveryType->getValue(),
            'DeliveryType must be pre-selected to CDP'
        );
    }

    /**
     * ClosestDropPoint must always be removed from admin packaging popup,
     * even when no versendenInfo is present (no checkout data).
     *
     * @test
     */
    public function closestDropPointRemovedFromAdminWithoutSelection()
    {
        /** @var Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Service_Edit $block */
        $block = Mage::app()->getLayout()->createBlock(self::EDIT_BLOCK_ALIAS);
        $block->getShipment()->getOrder()->setShippingMethod('dhlversenden_flatrate');

        /** @var \Dhl\Versenden\ParcelDe\Service\Collection $services */
        $services = $block->getServices();

        // ClosestDropPoint must be removed
        static::assertNull(
            $services->getItem(Service\ClosestDropPoint::CODE),
            'ClosestDropPoint must not appear in admin packaging popup even without checkout data'
        );

        // DeliveryType should remain with all options (no locking)
        $deliveryType = $services->getItem(Service\DeliveryType::CODE);
        static::assertNotNull($deliveryType, 'DeliveryType must remain in admin packaging popup');
        static::assertCount(3, $deliveryType->getOptions(), 'DeliveryType must keep all three options when no CDP selection');
    }
}
