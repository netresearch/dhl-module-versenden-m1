<?php

/**
 * See LICENSE.md for license details.
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
