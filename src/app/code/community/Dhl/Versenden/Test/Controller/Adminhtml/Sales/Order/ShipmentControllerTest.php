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
 * Dhl_Versenden_Test_Controller_Adminhtml_Sales_Order_ShipmentControllerTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Test_Controller_Adminhtml_Sales_Order_ShipmentControllerTest
    extends Dhl_Versenden_Test_Case_AdminController
{
    /**
     * @test
     * @registry current_shipment
     * @loadFixture Controller_ConfigTest
     */
    public function getShippingItemsGridAction()
    {
        $grid = '<table></table>';

        $blockMock = $this->getBlockMock(
            'dhl_versenden/adminhtml_sales_order_shipment_packaging_grid',
            array('renderView', 'displayCustomsValue')
        );
        $blockMock
            ->expects($this->once())
            ->method('renderView')
            ->willReturn($grid);
        $this->replaceByMock('block', 'dhl_versenden/adminhtml_sales_order_shipment_packaging_grid', $blockMock);

        $this->dispatch('adminhtml/sales_order_shipment/getShippingItemsGrid');
        $this->assertResponseBody($this->equalTo($grid));
    }
}
