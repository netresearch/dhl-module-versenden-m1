<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Test_Helper_DataTest extends EcomDev_PHPUnit_Test_Case
{
    const PACKAGING_BLOCK_ALIAS = 'dhl_versenden/adminhtml_sales_order_shipment_packaging';

    protected function mockPackagingBlock()
    {
        $order = Mage::getModel('sales/order');
        $shipment = new Varien_Object();
        $shipment->setData('order', $order);

        $blockMock = $this->getBlockMock(self::PACKAGING_BLOCK_ALIAS, array('getShipment', 'getTemplate'));
        $blockMock
            ->expects($this->any())
            ->method('getShipment')
            ->willReturn($shipment);
        Mage::getSingleton('core/layout')->setBlock(self::PACKAGING_BLOCK_ALIAS, $blockMock);
    }

    /**
     * @test
     */
    public function getModuleVersion()
    {
        $helper = Mage::helper('dhl_versenden/data');
        $this->assertRegExp('/\d\.\d{1,2}\.\d{1,2}/', $helper->getModuleVersion());
    }

    /**
     * @test
     */
    public function utcToCet()
    {
        $helper   = Mage::helper('dhl_versenden/data');

        $gmtDate = '2015-01-01 12:00:00';
        $cetDate = '2015-01-01 13:00:00';
        $this->assertSame($cetDate, $helper->utcToCet(strtotime($gmtDate)));


        $gmtDate = '2015-06-15 12:00:00';
        $cetDate = '2015-06-15 14:00:00';
        $this->assertSame($cetDate, $helper->utcToCet(strtotime($gmtDate)));

        $this->assertInternalType('string', $helper->utcToCet());
    }

    /**
     * @test
     * @loadFixture Model_ShipmentConfigTest
     */
    public function isCollectCustomsData()
    {
        $helper = Mage::helper('dhl_versenden/data');

        // shipper EU, receiver EU
        $shipperCountry = 'DE';
        $recipientCountry = 'ES';
        $recipientPostalCode = '28970';
        $this->assertFalse($helper->isCollectCustomsData($shipperCountry, $recipientCountry, $recipientPostalCode));

        // shipper EU, receiver EU, area with special customs regulations
        $shipperCountry = 'DE';
        $recipientCountry = 'ES';
        $recipientPostalCode = '35005';
        $this->assertTrue($helper->isCollectCustomsData($shipperCountry, $recipientCountry, $recipientPostalCode));

        // shipper EU, domestic shipment, postal code that looks like the one from a dutiable area of another country.
        $shipperCountry = 'DE';
        $recipientCountry = 'DE';
        $recipientPostalCode = '35005';
        $this->assertFalse($helper->isCollectCustomsData($shipperCountry, $recipientCountry, $recipientPostalCode));

        // shipper EU, receiver not EU
        $shipperCountry = 'DE';
        $recipientCountry = 'NZ';
        $recipientPostalCode = '1071';
        $this->assertTrue($helper->isCollectCustomsData($shipperCountry, $recipientCountry, $recipientPostalCode));

        // shipper EU, receiver not EU
        $shipperCountry = 'DE';
        $recipientCountry = 'GB';
        $recipientPostalCode = 'W1U 6BF';
        $this->assertTrue($helper->isCollectCustomsData($shipperCountry, $recipientCountry, $recipientPostalCode));

        // shipper EU, receiver not EU, area with special customs regulations
        $shipperCountry = 'DE';
        $recipientCountry = 'GB';
        $recipientPostalCode = 'BT6 0BZ';
        $this->assertFalse($helper->isCollectCustomsData($shipperCountry, $recipientCountry, $recipientPostalCode));

        // shipper not EU, receiver not EU, yet same country
        $shipperCountry = 'NZ';
        $recipientCountry = 'NZ';
        $recipientPostalCode = '1071';
        $this->assertFalse($helper->isCollectCustomsData($shipperCountry, $recipientCountry, $recipientPostalCode));

        // shipper not EU, receiver not EU, different countries
        $shipperCountry = 'AU';
        $recipientCountry = 'NZ';
        $recipientPostalCode = '1071';
        $this->assertTrue($helper->isCollectCustomsData($shipperCountry, $recipientCountry, $recipientPostalCode));
    }

    /**
     * @test
     */
    public function getPackagingTemplatesVersendenCarrier()
    {
        $this->mockPackagingBlock();

        $shippingMethod = 'dhlversenden_bar';
        $customTemplate = 'foo';

        /** @var Mage_Adminhtml_Block_Sales_Order_Shipment_Packaging $block */
        $block = Mage::getSingleton('core/layout')->getBlock(self::PACKAGING_BLOCK_ALIAS);
        $block->getShipment()->getOrder()->setShippingMethod($shippingMethod);


        $helper = new Dhl_Versenden_Helper_Data();

        $template = $helper->getPackagingPopupTemplate($customTemplate, self::PACKAGING_BLOCK_ALIAS);
        $this->assertEquals($customTemplate, $template);

        $template = $helper->getPackagingPackedTemplate($customTemplate, self::PACKAGING_BLOCK_ALIAS);
        $this->assertEquals($customTemplate, $template);
    }

    /**
     * @test
     */
    public function getPackagingTemplatesSomeCarrier()
    {
        $this->mockPackagingBlock();

        $shippingMethod  = 'foo_bar';
        $customTemplate  = 'foo';
        $defaultTemplate = 'fox';

        /** @var EcomDev_PHPUnit_Mock_Proxy|Mage_Adminhtml_Block_Sales_Order_Shipment_Packaging $block */
        $block = Mage::getSingleton('core/layout')->getBlock(self::PACKAGING_BLOCK_ALIAS);
        $block->getShipment()->getOrder()->setShippingMethod($shippingMethod);
        $block
            ->expects($this->exactly(2))
            ->method('getTemplate')
            ->willReturn($defaultTemplate);


        $helper = new Dhl_Versenden_Helper_Data();

        $template = $helper->getPackagingPopupTemplate($customTemplate, self::PACKAGING_BLOCK_ALIAS);
        $this->assertEquals($defaultTemplate, $template);

        $template = $helper->getPackagingPackedTemplate($customTemplate, self::PACKAGING_BLOCK_ALIAS);
        $this->assertEquals($defaultTemplate, $template);
    }

    /**
     * @test
     */
    public function addStatusHistoryComment()
    {
        $helper = new Dhl_Versenden_Helper_Data();

        $history = $this->getMockBuilder(Mage_Sales_Model_Resource_Order_Status_History_Collection::class)
            ->setMethods(array('save'))
            ->getMock();

        $order = $this->getMockBuilder(Mage_Sales_Model_Order::class)
            ->setMethods(array('getStatusHistoryCollection'))
            ->getMock();
        $order
            ->expects($this->exactly(2))
            ->method('getStatusHistoryCollection')
            ->willReturn($history);

        $comment = 'status comment';

        /** @var Mage_Sales_Model_Order $order */
        /** @var Mage_Sales_Model_Resource_Order_Status_History_Collection $history */
        $this->assertCount(0, $history);
        $helper->addStatusHistoryComment($order, $comment);
        $this->assertCount(1, $history);
        $helper->addStatusHistoryComment($order, $comment);
        $this->assertCount(2, $history);

        /** @var Mage_Sales_Model_Order_Status_History $item */
        foreach ($history as $item) {
            $this->assertEquals($comment, $item->getComment());
        }
    }
}
