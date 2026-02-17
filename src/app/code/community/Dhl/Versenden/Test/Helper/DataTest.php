<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Test_Helper_DataTest extends EcomDev_PHPUnit_Test_Case
{
    public const PACKAGING_BLOCK_ALIAS = 'dhl_versenden/adminhtml_sales_order_shipment_packaging';

    protected function mockPackagingBlock()
    {
        $order = Mage::getModel('sales/order');
        $shipment = new Varien_Object();
        $shipment->setData('order', $order);

        $blockMock = $this->getBlockMock(self::PACKAGING_BLOCK_ALIAS, ['getShipment', 'getTemplate']);
        $blockMock
            ->expects(static::any())
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
        static::assertMatchesRegularExpression('/\d\.\d{1,2}\.\d{1,2}/', $helper->getModuleVersion());
    }

    /**
     * @test
     */
    public function utcToCet()
    {
        $helper   = Mage::helper('dhl_versenden/data');

        $gmtDate = '2015-01-01 12:00:00';
        $cetDate = '2015-01-01 13:00:00';
        static::assertSame($cetDate, $helper->utcToCet(strtotime($gmtDate)));


        $gmtDate = '2015-06-15 12:00:00';
        $cetDate = '2015-06-15 14:00:00';
        static::assertSame($cetDate, $helper->utcToCet(strtotime($gmtDate)));

        static::assertIsString($helper->utcToCet());
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
        static::assertFalse($helper->isCollectCustomsData($shipperCountry, $recipientCountry, $recipientPostalCode));

        // shipper EU, receiver EU, area with special customs regulations
        $shipperCountry = 'DE';
        $recipientCountry = 'ES';
        $recipientPostalCode = '35005';
        static::assertTrue($helper->isCollectCustomsData($shipperCountry, $recipientCountry, $recipientPostalCode));

        // shipper EU, domestic shipment, postal code that looks like the one from a dutiable area of another country.
        $shipperCountry = 'DE';
        $recipientCountry = 'DE';
        $recipientPostalCode = '35005';
        static::assertFalse($helper->isCollectCustomsData($shipperCountry, $recipientCountry, $recipientPostalCode));

        // shipper EU, receiver not EU
        $shipperCountry = 'DE';
        $recipientCountry = 'NZ';
        $recipientPostalCode = '1071';
        static::assertTrue($helper->isCollectCustomsData($shipperCountry, $recipientCountry, $recipientPostalCode));

        // shipper EU, receiver not EU
        $shipperCountry = 'DE';
        $recipientCountry = 'GB';
        $recipientPostalCode = 'W1U 6BF';
        static::assertTrue($helper->isCollectCustomsData($shipperCountry, $recipientCountry, $recipientPostalCode));

        // shipper EU, receiver not EU, area with special customs regulations
        $shipperCountry = 'DE';
        $recipientCountry = 'GB';
        $recipientPostalCode = 'BT6 0BZ';
        static::assertFalse($helper->isCollectCustomsData($shipperCountry, $recipientCountry, $recipientPostalCode));

        // shipper not EU, receiver not EU, yet same country
        $shipperCountry = 'NZ';
        $recipientCountry = 'NZ';
        $recipientPostalCode = '1071';
        static::assertFalse($helper->isCollectCustomsData($shipperCountry, $recipientCountry, $recipientPostalCode));

        // shipper not EU, receiver not EU, different countries
        $shipperCountry = 'AU';
        $recipientCountry = 'NZ';
        $recipientPostalCode = '1071';
        static::assertTrue($helper->isCollectCustomsData($shipperCountry, $recipientCountry, $recipientPostalCode));
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
        static::assertEquals($customTemplate, $template);

        $template = $helper->getPackagingPackedTemplate($customTemplate, self::PACKAGING_BLOCK_ALIAS);
        static::assertEquals($customTemplate, $template);
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
            ->expects(static::exactly(2))
            ->method('getTemplate')
            ->willReturn($defaultTemplate);


        $helper = new Dhl_Versenden_Helper_Data();

        $template = $helper->getPackagingPopupTemplate($customTemplate, self::PACKAGING_BLOCK_ALIAS);
        static::assertEquals($defaultTemplate, $template);

        $template = $helper->getPackagingPackedTemplate($customTemplate, self::PACKAGING_BLOCK_ALIAS);
        static::assertEquals($defaultTemplate, $template);
    }

    /**
     * @test
     */
    public function addStatusHistoryComment()
    {
        $helper = new Dhl_Versenden_Helper_Data();

        // Track items manually instead of relying on collection count
        $items = [];

        $history = $this->getMockBuilder(Mage_Sales_Model_Resource_Order_Status_History_Collection::class)
            ->setMethods(['addItem', 'save'])
            ->getMock();

        $history
            ->method('addItem')
            ->willReturnCallback(function ($item) use (&$items) {
                $items[] = $item;
            });

        $order = $this->getMockBuilder(Mage_Sales_Model_Order::class)
            ->setMethods(['getStatusHistoryCollection', 'getStatus'])
            ->getMock();
        $order
            ->expects(static::exactly(2))
            ->method('getStatusHistoryCollection')
            ->willReturn($history);
        $order
            ->expects(static::exactly(2))
            ->method('getStatus')
            ->willReturn('processing');

        $comment = 'status comment';

        /** @var Mage_Sales_Model_Order $order */
        static::assertCount(0, $items);
        $helper->addStatusHistoryComment($order, $comment);
        static::assertCount(1, $items);
        $helper->addStatusHistoryComment($order, $comment);
        static::assertCount(2, $items);

        /** @var Mage_Sales_Model_Order_Status_History $item */
        foreach ($items as $item) {
            static::assertEquals($comment, $item->getComment());
        }
    }
}
