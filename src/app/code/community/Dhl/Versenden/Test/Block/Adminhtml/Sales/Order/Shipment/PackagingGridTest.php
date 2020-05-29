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
 * Dhl_Versenden_Test_Block_Adminhtml_Sales_Order_Shipment_PackagingTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Test_Block_Adminhtml_Sales_Order_Shipment_PackagingGridTest
    extends EcomDev_PHPUnit_Test_Case
{
    const BLOCK_ALIAS = 'dhl_versenden/adminhtml_sales_order_shipment_packaging_grid';

    /**
     * Mock getShipment registry access
     * @see Mage_Adminhtml_Block_Sales_Order_Shipment_Packaging_Grid::getShipment()
     */
    protected function setUp()
    {
        parent::setUp();

        $shipment = Mage::getModel('sales/order_shipment')->load(10);

        $blockMock = $this->getBlockMock(self::BLOCK_ALIAS, array('getShipment', 'displayCustomsValue'));
        $blockMock
            ->expects($this->any())
            ->method('getShipment')
            ->willReturn($shipment);
        $this->replaceByMock('block', self::BLOCK_ALIAS, $blockMock);
    }

    /**
     * @test
     * @loadFixture Model_ShipmentConfigTest
     */
    public function setTemplate()
    {
        $customTemplate = 'dhl_versenden/sales/packaging_grid.phtml';
        $defaultTemplate = 'sales/order/shipment/packaging/grid.phtml';

        // additional customs data required
        $shippingAddress = Mage::getModel('sales/order_address');
        $shippingAddress->setCountryId('NZ');

        $order = Mage::getModel('sales/order');
        $order->setShippingAddress($shippingAddress);

        $shipment = Mage::getModel('sales/order_shipment');
        $shipment->setStoreId(1);
        $shipment->setOrder($order);
        Mage::register('current_shipment', $shipment);

        $block = new Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Packaging_Grid();
        $this->assertEquals($customTemplate, $block->getTemplate());

        Mage::unregister('current_shipment');


        // no customs data required
        $shippingAddress->setCountryId('DE');
        Mage::register('current_shipment', $shipment);

        $block = new Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Packaging_Grid();
        $this->assertEquals($defaultTemplate, $block->getTemplate());

        Mage::unregister('current_shipment');
    }

    /**
     * @test
     */
    public function getCountries()
    {
        $countryDE = 'DE';
        $countryXY = 'XY';
        $countries = array($countryDE, $countryXY);

        $mock = $this->getModelMock('adminhtml/system_config_source_country', array('toOptionArray'));
        $mock
            ->expects($this->once())
            ->method('toOptionArray')
            ->willReturn($countries);
        $this->replaceByMock('singleton', 'adminhtml/system_config_source_country', $mock);

        /** @var Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Packaging_Grid $block */
        $block = Mage::app()->getLayout()->createBlock(self::BLOCK_ALIAS);
        $this->assertEquals($countries, $block->getCountries());
    }

    /**
     * @test
     * @loadFixture Block_PackagingGridTest
     */
    public function getCountryOfManufacture()
    {
        /** @var Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Packaging_Grid $block */
        $block = Mage::app()->getLayout()->createBlock(self::BLOCK_ALIAS);
        $this->assertEquals('TR', $block->getCountryOfManufacture('100'));
        $this->assertEquals('DE', $block->getCountryOfManufacture('200'));
    }
}
