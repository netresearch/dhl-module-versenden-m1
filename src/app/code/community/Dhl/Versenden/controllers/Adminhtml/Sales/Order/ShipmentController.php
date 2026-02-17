<?php

/**
 * See LICENSE.md for license details.
 */

require_once Mage::getModuleDir('controllers', 'Mage_Adminhtml') . '/Sales/Order/ShipmentController.php';

class Dhl_Versenden_Adminhtml_Sales_Order_ShipmentController extends Mage_Adminhtml_Sales_Order_ShipmentController
{
    /**
     * Set custom grid block in order to update the grid template
     *
     * @return Mage_Core_Controller_Response_Http
     */
    public function getShippingItemsGridAction()
    {
        $this->_initShipment();
        return $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('dhl_versenden/adminhtml_sales_order_shipment_packaging_grid')
                ->setIndex($this->getRequest()->getParam('index'))
                ->toHtml(),
        );
    }
}
