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
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */

/**
 * Dhl_Versenden_Test_Block_Adminhtml_Sales_Order_Shipment_ServiceTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Test_Block_Adminhtml_Sales_Order_Shipment_ServiceTest
    extends EcomDev_PHPUnit_Test_Case
{
    const EDIT_BLOCK_ALIAS = 'dhl_versenden/adminhtml_sales_order_shipment_service_edit';
    const VIEW_BLOCK_ALIAS = 'dhl_versenden/adminhtml_sales_order_shipment_service_view';

    protected function mockEditBlock()
    {
        $shippingAddress = Mage::getModel('sales/order_address');
        $shippingAddress->setCountryId('DE');

        $order = Mage::getModel('sales/order');
        $order->setShippingAddress($shippingAddress);
        $order->setShippingMethod('dhlversenden_flatrate');

        $shipment = Mage::getModel('sales/order_shipment');
        $shipment->setStoreId(1);
        $shipment->setOrder($order);

        $editBlockMock = $this->getBlockMock(self::EDIT_BLOCK_ALIAS, array('getShipment', 'fetchView'));
        $editBlockMock
            ->expects($this->any())
            ->method('getShipment')
            ->willReturn($shipment);
        $this->replaceByMock('block', self::EDIT_BLOCK_ALIAS, $editBlockMock);
    }

    protected function mockViewBlock()
    {
        $shippingAddress = Mage::getModel('sales/order_address');
        $shippingAddress->setCountryId('DE');

        $order = Mage::getModel('sales/order');
        $order->setShippingAddress($shippingAddress);
        $order->setShippingMethod('dhlversenden_flatrate');

        $shipment = Mage::getModel('sales/order_shipment');
        $shipment->setStoreId(1);
        $shipment->setOrder($order);

        $editBlockMock = $this->getBlockMock(self::VIEW_BLOCK_ALIAS, array('getShipment'));
        $editBlockMock
            ->expects($this->any())
            ->method('getShipment')
            ->willReturn($shipment);
        $this->replaceByMock('block', self::VIEW_BLOCK_ALIAS, $editBlockMock);
    }

    protected function setUp()
    {
        parent::setUp();

        /**
         * Loading self::EDIT_BLOCK_ALIAS currently crashes because the registry contains no "current_shipment" object.
         * Please fix if you know how.
         */
        // $this->mockEditBlock();
        // $this->mockViewBlock();
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function renderViewWrongShippingMethod()
    {
        $this->markTestIncomplete('This currently crashes because the registry contains no "current_shipment" object. Please fix if you know how.');

        /** @var EcomDev_PHPUnit_Mock_Proxy|Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Service_Edit $block */
        $block = Mage::app()->getLayout()->createBlock(self::EDIT_BLOCK_ALIAS);
        $block->getShipment()->getOrder()->setShippingMethod('flatrate_flatrate');
        $block
            ->expects($this->never())
            ->method('fetchView');

        /** @var Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Service_Edit $block */
        $this->assertEmpty($block->renderView());
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function renderViewDhlShippingMethod()
    {
        $blockHtml = 'foo';

        $this->markTestIncomplete('This currently crashes because the registry contains no "current_shipment" object. Please fix if you know how.');

        /** @var EcomDev_PHPUnit_Mock_Proxy|Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Service_Edit $block */
        $block = Mage::app()->getLayout()->createBlock(self::EDIT_BLOCK_ALIAS);
        $block->getShipment()->getOrder()->setShippingMethod('dhlversenden_flatrate');
        $block
            ->expects($this->exactly(1))
            ->method('fetchView')
            ->willReturn($blockHtml);

        $this->assertEquals($blockHtml, $block->renderView());
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function selectedServicesForEdit()
    {
        $preferredLocation = 'Garage';

        $info = new \Dhl\Versenden\Bcs\Api\Info();
        $info->getServices()->bulkyGoods = true;
        $info->getServices()->preferredLocation = $preferredLocation;

        $this->markTestIncomplete('This currently crashes because the registry contains no "current_shipment" object. Please fix if you know how.');

        /** @var EcomDev_PHPUnit_Mock_Proxy|Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Service_Edit $block */
        $block = Mage::app()->getLayout()->createBlock(self::EDIT_BLOCK_ALIAS);
        $block->getShipment()->getOrder()->setShippingMethod('dhlversenden_flatrate');
        $block->getShipment()->getOrder()->getShippingAddress()->setData('dhl_versenden_info', $info);

        /** @var \Dhl\Versenden\Bcs\Api\Shipment\Service\Collection $services */
        $services = $block->getServices();
        $this->assertInstanceOf('\Dhl\Versenden\Bcs\Api\Shipment\Service\Collection', $services);
        $this->assertContainsOnly('\Dhl\Versenden\Bcs\Api\Shipment\Service\Type\Generic', $services);

        // bulkyGoods disabled via config
        $code = \Dhl\Versenden\Bcs\Api\Shipment\Service\BulkyGoods::CODE;
        $this->assertTrue($services->getItem($code)->isEnabled());

        // preferredLocation enabled via config and preselected via dhl_versenden_info
        $code = \Dhl\Versenden\Bcs\Api\Shipment\Service\PreferredLocation::CODE;
        $this->assertEquals('Garage', $services->getItem($code)->getValue());
    }

    /**
     * @test
     */
    public function allServicesForEdit()
    {
        $this->markTestIncomplete('This currently crashes because the registry contains no "current_shipment" object. Please fix if you know how.');

        /** @var EcomDev_PHPUnit_Mock_Proxy|Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Service_Edit $block */
        $block = Mage::app()->getLayout()->createBlock(self::EDIT_BLOCK_ALIAS);
        $block->getShipment()->getOrder()->setShippingMethod('dhlversenden_flatrate');

        /** @var \Dhl\Versenden\Bcs\Api\Shipment\Service\Collection $services */
        $services = $block->getServices();
        $this->assertInstanceOf('\Dhl\Versenden\Bcs\Api\Shipment\Service\Collection', $services);
        $this->assertContainsOnly('\Dhl\Versenden\Bcs\Api\Shipment\Service\Type\Generic', $services);

        /** @var Dhl\Versenden\Bcs\Api\Shipment\Service\Type\Generic $service */
        foreach ($services as $service) {
            if ($service->getCode() === \Dhl\Versenden\Bcs\Api\Shipment\Service\PrintOnlyIfCodeable::CODE) {
                // PrintOnlyIfCodeable is enabled via config
                $this->assertTrue($service->isSelected());
            } else {
                $this->assertFalse($service->isSelected());
            }
        }
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function selectedServicesForView()
    {
        $this->markTestIncomplete('This currently crashes because the registry contains no "current_shipment" object. Please fix if you know how.');

        $preferredLocation = 'Garage';

        $info = new \Dhl\Versenden\Bcs\Api\Info();
        $info->getServices()->bulkyGoods = true;
        $info->getServices()->preferredLocation = $preferredLocation;

        /** @var EcomDev_PHPUnit_Mock_Proxy|Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Service_View $block */
        $block = Mage::app()->getLayout()->createBlock(self::VIEW_BLOCK_ALIAS);
        $block->getShipment()->getOrder()->setShippingMethod('dhlversenden_flatrate');
        $block->getShipment()->getOrder()->getShippingAddress()->setData('dhl_versenden_info', $info);

        /** @var \Dhl\Versenden\Bcs\Api\Shipment\Service\Collection $services */
        $services = $block->getServices();
        $this->assertInstanceOf('\Dhl\Versenden\Bcs\Api\Shipment\Service\Collection', $services);
        $this->assertContainsOnly('\Dhl\Versenden\Bcs\Api\Shipment\Service\Type\Generic', $services);

        // bulkyGoods disabled via config but preselected via dhl_versenden_info
        $code = \Dhl\Versenden\Bcs\Api\Shipment\Service\BulkyGoods::CODE;
        $this->assertTrue($services->getItem($code)->getValue());

        // preferredLocation enabled via config and preselected via dhl_versenden_info
        $code = \Dhl\Versenden\Bcs\Api\Shipment\Service\PreferredLocation::CODE;
        $this->assertEquals('Garage', $services->getItem($code)->getValue());
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function allServicesForView()
    {
        $this->markTestIncomplete('This currently crashes because the registry contains no "current_shipment" object. Please fix if you know how.');

        /** @var EcomDev_PHPUnit_Mock_Proxy|Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Service_View $block */
        $block = Mage::app()->getLayout()->createBlock(self::VIEW_BLOCK_ALIAS);
        $block->getShipment()->getOrder()->setShippingMethod('dhlversenden_flatrate');

        /** @var \Dhl\Versenden\Bcs\Api\Shipment\Service\Collection $services */
        $services = $block->getServices();
        $this->assertInstanceOf('\Dhl\Versenden\Bcs\Api\Shipment\Service\Collection', $services);
        $this->assertContainsOnly('\Dhl\Versenden\Bcs\Api\Shipment\Service\Type\Generic', $services);

        /** @var Dhl\Versenden\Bcs\Api\Shipment\Service\Type\Generic $service */
        foreach ($services as $service) {
            if ($service->getCode() === \Dhl\Versenden\Bcs\Api\Shipment\Service\PrintOnlyIfCodeable::CODE) {
                // PrintOnlyIfCodeable is enabled via config
                $this->assertTrue($service->isSelected());
            } else {
                $this->assertFalse($service->isSelected());
            }
        }
    }

    /**
     * @test
     */
    public function getRendererForEdit()
    {
        $this->markTestIncomplete('This currently crashes because the registry contains no "current_shipment" object. Please fix if you know how.');

        $block = Mage::app()->getLayout()->createBlock('dhl_versenden/adminhtml_sales_order_shipment_service_edit');

        $location = 'Melmac';
        $service = new \Dhl\Versenden\Bcs\Api\Shipment\Service\PreferredLocation('', true, true, '');
        $service->setValue($location);

        $renderer = $block->getRenderer($service);
        $this->assertNotEquals($location, $renderer->getValueHtml());
    }

    /**
     * @test
     */
    public function getRendererForView()
    {
        $this->markTestIncomplete('This currently crashes because the registry contains no "current_shipment" object. Please fix if you know how.');

        $block = Mage::app()->getLayout()->createBlock('dhl_versenden/adminhtml_sales_order_shipment_service_view');

        $location = 'Melmac';
        $service = new \Dhl\Versenden\Bcs\Api\Shipment\Service\PreferredLocation('', true, true, '');
        $service->setValue($location);

        $renderer = $block->getRenderer($service);
        $this->assertEquals($location, $renderer->getValueHtml());
    }
}
