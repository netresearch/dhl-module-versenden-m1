<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Test_Block_Adminhtml_Sales_Order_Shipment_PackagingTest
    extends EcomDev_PHPUnit_Test_Case
{
    const BLOCK_ALIAS = 'dhl_versenden/adminhtml_sales_order_shipment_packaging';

    /**
     * Mock getShipment registry access
     * @see Mage_Adminhtml_Block_Sales_Order_Shipment_Packaging::getShipment()
     */
    protected function setUp()
    {
        parent::setUp();

        $shippingAddress = Mage::getModel('sales/order_address');
        $shippingAddress->setCountryId('DE');

        $order = Mage::getModel('sales/order');
        $order->setShippingAddress($shippingAddress);

        $shipment = Mage::getModel('sales/order_shipment');
        $shipment->setStoreId(1);
        $shipment->setOrder($order);

        $blockMock = $this->getBlockMock(self::BLOCK_ALIAS, array('getShipment'));
        $blockMock
            ->expects($this->any())
            ->method('getShipment')
            ->willReturn($shipment);
        $this->replaceByMock('block', self::BLOCK_ALIAS, $blockMock);
    }

    /**
     * @test
     * @loadFixture Block_PackagingTest
     */
    public function getStoreUnit()
    {
        /** @var Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Packaging $block */
        $block = Mage::app()->getLayout()->createBlock(self::BLOCK_ALIAS);

        $block->getShipment()->setStoreId(1);
        $this->assertEquals('G', $block->getStoreUnit());

        $block->getShipment()->setStoreId(2);
        $this->assertEquals('KG', $block->getStoreUnit());
    }

    /**
     * @test
     */
    public function getWeightUnits()
    {
        /** @var Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Packaging $block */
        $block = Mage::app()->getLayout()->createBlock(self::BLOCK_ALIAS);

        $units = array('foo', 'bar');
        $carrierMock = $this->getModelMock('dhl_versenden/shipping_carrier_versenden', array('getCode'));
        $carrierMock
            ->expects($this->once())
            ->method('getCode')
            ->with($this->equalTo('unit_of_measure'))
            ->willReturn($units);
        $this->replaceByMock('singleton', 'dhl_versenden/shipping_carrier_versenden', $carrierMock);

        $this->assertSame($units, $block->getWeightUnits());
    }

    /**
     * @test
     * @loadFixture Block_PackagingTest
     */
    public function displayCustomsValue()
    {
        /** @var Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Packaging $block */
        $block = Mage::app()->getLayout()->createBlock(self::BLOCK_ALIAS);

        $eu  = false;
        $row = true;

        $helperMock = $this->getHelperMock('dhl_versenden/data', array('isCollectCustomsData'));
        $helperMock
            ->expects($this->exactly(2))
            ->method('isCollectCustomsData')
            ->willReturnOnConsecutiveCalls($eu, $row);
        $this->replaceByMock('helper', 'dhl_versenden/data', $helperMock);

        $this->assertSame($eu, $block->displayCustomsValue());
        $this->assertSame($row, $block->displayCustomsValue());
    }
}
