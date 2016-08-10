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
    /**
     * @test
     */
    public function getModuleVersion()
    {
        $helper = Mage::helper('dhl_versenden/data');
        $this->assertRegExp('/\d\.\d{1,2}\.\d{1,2}/', $helper->getModuleVersion());
    }

    /**
     * @param string $street
     *
     * @test
     * @loadExpectation
     * @dataProvider dataProvider
     */
    public function splitStreet($street)
    {
        $helper   = Mage::helper('dhl_versenden/data');
        $split    = $helper->splitStreet($street);
        $expected = $this->expected('auto')->getData();

        $this->assertEquals($expected, $split);
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
    public function calculateItemsWeight()
    {
        $helper = Mage::helper('dhl_versenden/data');

        $defaultWeight =  0.2;

        $weightOne = 0.2;
        $weightTwo = 1.2;
        $weightThree = null;

        $weightInKg = $weightOne + $weightTwo + $defaultWeight;

        $itemOne = new Mage_Sales_Model_Order_Shipment_Item();
        $itemOne->setWeight($weightOne);

        $itemTwo = new Mage_Sales_Model_Order_Shipment_Item();
        $itemTwo->setWeight($weightTwo);

        $itemThree = new Mage_Sales_Model_Order_Shipment_Item();
        $itemThree->setWeight($weightThree);

        $items = array($itemOne, $itemTwo, $itemThree);
        $this->assertEquals($weightInKg, $helper->calculateItemsWeight($items));


        $weightOne = 200;
        $weightTwo = 1200;
        $weightThree = null;

        $itemOne = new Mage_Sales_Model_Order_Shipment_Item();
        $itemOne->setWeight($weightOne);

        $itemTwo = new Mage_Sales_Model_Order_Shipment_Item();
        $itemTwo->setWeight($weightTwo);

        $itemThree = new Mage_Sales_Model_Order_Shipment_Item();
        $itemThree->setWeight($weightThree);

        $items = array($itemOne, $itemTwo, $itemThree);
        $this->assertEquals($weightInKg, $helper->calculateItemsWeight($items, 200, 'G'));
    }

    /**
     * @test
     */
    public function getPackagingPopupTemplateVersendenCarrier()
    {
        $helper = Mage::helper('dhl_versenden/data');

        $shippingMethod = 'dhlversenden_bar';
        $customTemplate = 'foo';
        $blockType      = 'dhl_versenden/adminhtml_sales_order_shipment_packaging';

        $order = new Mage_Sales_Model_Order();
        $order->setShippingMethod($shippingMethod);

        $shipment = new Varien_Object();
        $shipment->setData('order', $order);

        $blockMock = $this->getBlockMock(
            $blockType,
            array('getShipment')
        );
        $blockMock
            ->expects($this->once())
            ->method('getShipment')
            ->willReturn($shipment);
        Mage::getSingleton('core/layout')->setBlock($blockType, $blockMock);

        $template = $helper->getPackagingPopupTemplate($customTemplate, $blockType);
        $this->assertEquals($customTemplate, $template);
    }

    /**
     * @test
     */
    public function getPackagingPopupTemplateSomeCarrier()
    {
        $helper = Mage::helper('dhl_versenden/data');

        $shippingMethod  = 'foo_bar';
        $customTemplate  = 'foo';
        $defaultTemplate = 'fox';
        $blockType       = 'dhl_versenden/adminhtml_sales_order_shipment_packaging';

        $order = new Mage_Sales_Model_Order();
        $order->setShippingMethod($shippingMethod);

        $shipment = new Varien_Object();
        $shipment->setData('order', $order);

        $blockMock = $this->getBlockMock(
            $blockType,
            array('getShipment', 'getTemplate')
        );
        $blockMock
            ->expects($this->once())
            ->method('getShipment')
            ->willReturn($shipment);
        $blockMock
            ->expects($this->once())
            ->method('getTemplate')
            ->willReturn($defaultTemplate);
        Mage::getSingleton('core/layout')->setBlock($blockType, $blockMock);

        $template = $helper->getPackagingPopupTemplate($customTemplate, $blockType);
        $this->assertEquals($defaultTemplate, $template);
    }
}
