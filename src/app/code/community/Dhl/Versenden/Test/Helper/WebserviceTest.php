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
use \Dhl\Versenden\Webservice\RequestData\ShipmentOrder;
/**
 * Dhl_Versenden_Test_Helper_WebserviceTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Test_Helper_WebserviceTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     */
    public function addStatusHistoryComment()
    {
        $helper = new Dhl_Versenden_Helper_Webservice();

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
