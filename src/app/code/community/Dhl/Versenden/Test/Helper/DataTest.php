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
 * Dhl_Versenden_Test_Helper_DataTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
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
     */
    public function isCollectCustomsData()
    {
        $helper = Mage::helper('dhl_versenden/data');

        // shipper EU, receiver EU
        $shipperCountry = 'DE';
        $recipientCountry = 'ES';
        $this->assertFalse($helper->isCollectCustomsData($shipperCountry, $recipientCountry));

        // shipper EU, receiver not EU
        $shipperCountry = 'DE';
        $recipientCountry = 'NZ';
        $this->assertTrue($helper->isCollectCustomsData($shipperCountry, $recipientCountry));

        // shipper not EU, receiver not EU, yet same country
        $shipperCountry = 'NZ';
        $recipientCountry = 'NZ';
        $this->assertFalse($helper->isCollectCustomsData($shipperCountry, $recipientCountry));

        // shipper not EU, receiver not EU, different countries
        $shipperCountry = 'AU';
        $recipientCountry = 'NZ';
        $this->assertTrue($helper->isCollectCustomsData($shipperCountry, $recipientCountry));
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
        $helper->addStatusHistoryInfo($order, $comment);
        $this->assertCount(1, $history);
        $helper->addStatusHistoryError($order, $comment);
        $this->assertCount(2, $history);

        /** @var Mage_Sales_Model_Order_Status_History $item */
        foreach ($history as $item) {
            $this->assertStringEndsWith($comment, $item->getComment());
        }
    }
}
