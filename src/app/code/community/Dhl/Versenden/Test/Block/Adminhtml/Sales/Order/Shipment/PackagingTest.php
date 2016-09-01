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
class Dhl_Versenden_Test_Block_Adminhtml_Sales_Order_Shipment_PackagingTest
    extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     * @loadFixture Block_PackagingTest
     */
    public function getStoreUnit()
    {
        $blockType = 'dhl_versenden/adminhtml_sales_order_shipment_packaging';

        $shipmentOne = new Mage_Sales_Model_Order_Shipment();
        $shipmentOne->setStoreId(1);
        $shipmentTwo = new Mage_Sales_Model_Order_Shipment();
        $shipmentTwo->setStoreId(2);

        $blockMock = $this->getBlockMock($blockType, array('getShipment'));
        $blockMock
            ->expects($this->exactly(2))
            ->method('getShipment')
            ->willReturnOnConsecutiveCalls($shipmentOne, $shipmentTwo);
        $this->replaceByMock('block', $blockType, $blockMock);

        /** @var Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Packaging $block */
        $block = Mage::app()->getLayout()->createBlock($blockType);
        $storeUnitOne = $block->getStoreUnit();
        $storeUnitTwo = $block->getStoreUnit();

        $this->assertEquals($storeUnitOne, 'G');
        $this->assertEquals($storeUnitTwo, 'KG');
    }

    /**
     * @test
     */
    public function getWeightUnits()
    {
        $blockType = 'dhl_versenden/adminhtml_sales_order_shipment_packaging';
        /** @var Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Packaging $block */
        $block = Mage::app()->getLayout()->createBlock($blockType);

        $weightUnits = $block->getWeightUnits();
        $this->assertInternalType('array', $weightUnits);
        $this->assertCount(2, $weightUnits);
        $this->assertArrayHasKey('G', $weightUnits);
        $this->assertArrayHasKey('KG', $weightUnits);
    }

    /**
     * @test
     * @loadFixture Block_PackagingTest
     */
    public function displayCustomsValue()
    {
        $blockType = 'dhl_versenden/adminhtml_sales_order_shipment_packaging';

        // shipper EU, receiver EU
        $shipmentOne = new Mage_Sales_Model_Order_Shipment();
        $shippingAddressOne = new Mage_Sales_Model_Order_Address();
        $shippingAddressOne->setCountryId('ES');
        $orderOne = new Mage_Sales_Model_Order();
        $orderOne->setStoreId(1);
        $orderOne->setShippingAddress($shippingAddressOne);
        $shipmentOne->setOrder($orderOne);

        // shipper EU, receiver not EU
        $shipmentTwo = new Mage_Sales_Model_Order_Shipment();
        $shippingAddressTwo = new Mage_Sales_Model_Order_Address();
        $shippingAddressTwo->setCountryId('NZ');
        $orderTwo = new Mage_Sales_Model_Order();
        $orderTwo->setStoreId(1);
        $orderTwo->setShippingAddress($shippingAddressTwo);
        $shipmentTwo->setOrder($orderTwo);

        // shipper not EU, receiver not EU, same country
        $shipmentThree = new Mage_Sales_Model_Order_Shipment();
        $shippingAddressThree = new Mage_Sales_Model_Order_Address();
        $shippingAddressThree->setCountryId('NZ');
        $orderThree = new Mage_Sales_Model_Order();
        $orderThree->setStoreId(2);
        $orderThree->setShippingAddress($shippingAddressThree);
        $shipmentThree->setOrder($orderThree);

        // shipper not EU, receiver not EU, different countries
        $shipmentFour = new Mage_Sales_Model_Order_Shipment();
        $shippingAddressFour = new Mage_Sales_Model_Order_Address();
        $shippingAddressFour->setCountryId('AU');
        $orderFour = new Mage_Sales_Model_Order();
        $orderFour->setStoreId(2);
        $orderFour->setShippingAddress($shippingAddressFour);
        $shipmentFour->setOrder($orderFour);

        $blockMock = $this->getBlockMock($blockType, array('getShipment'));
        $blockMock
            ->expects($this->exactly(8))
            ->method('getShipment')
            ->willReturnOnConsecutiveCalls(
                $shipmentOne, $shipmentOne,
                $shipmentTwo, $shipmentTwo,
                $shipmentThree, $shipmentThree,
                $shipmentFour, $shipmentFour
            );
        $this->replaceByMock('block', $blockType, $blockMock);

        /** @var Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Packaging $block */
        $block = Mage::app()->getLayout()->createBlock($blockType);
        $this->assertFalse($block->displayCustomsValue());
        $this->assertTrue($block->displayCustomsValue());
        $this->assertFalse($block->displayCustomsValue());
        $this->assertTrue($block->displayCustomsValue());
    }
}
