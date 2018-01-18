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
 * @author    Andreas Müller <andreas.mueller@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */

/**
 * Dhl_Versenden_Adminhtml_Sales_Order_AutocreateController
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Andreas Müller <andreas.mueller@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Adminhtml_Sales_Order_AutocreateController extends Mage_Adminhtml_Controller_Action
{
    const MESSAGE_NO_APPLICABLE_ORDERS = 'Orders could not be processed with DHL Versenden.';
    const MESSAGE_LABELS_CREATED = '%d labels were created for %d orders.';
    const MESSAGE_LABELS_FAILED = 'The following orders had errors: %s.';

    /**
     * MassAction for Label Creation
     */
    public function massCreateShipmentLabelAction()
    {
        $orderIds = $this->getRequest()->getParam('order_ids');
        /** @var Dhl_Versenden_Model_Resource_Autocreate_Collection $collection */
        $collection = Mage::getResourceModel('dhl_versenden/autocreate_collection');
        $collection->addFieldToFilter('entity_id', array('in' => $orderIds));
        $collection->addShippingMethodFilter();
        $collection->addShipmentFilter();

        if ($collection->getSize() < 1) {
            $this->_getSession()->addError(self::MESSAGE_NO_APPLICABLE_ORDERS);
            $this->_redirect('adminhtml/sales_order');
            return;
        }

        $writer = Mage::getSingleton('dhl_versenden/logger_writer');
        $psrLogger = new Dhl_Versenden_Model_Logger_Mage($writer);
        $config = Mage::getModel('dhl_versenden/config');
        /** @var Dhl_Versenden_Model_Log $dhlLogger */
        $dhlLogger = Mage::getSingleton('dhl_versenden/log', array('config' => $config));
        $dhlLogger->setLogger($psrLogger);

        /** @var Dhl_Versenden_Model_Shipping_Autocreate $autocreate */
        $autocreate = Mage::getSingleton('dhl_versenden/shipping_autocreate', array('logger' => $dhlLogger));

        try {
            $num = $autocreate->autoCreate($collection);
            $creationMessage = sprintf(self::MESSAGE_LABELS_CREATED, $num, $collection->getSize());

            /** @var Mage_Sales_Model_Order $order */
            foreach ($collection->getItems() as $order) {
                if (!$order->hasShipments()) {
                    $increment = $order->getIncrementId();
                    $failedIncrements[$order->getEntityId()] = $increment;
                }
            }

            if (!empty($failedIncrements)) {
                $errorMessage = sprintf(self::MESSAGE_LABELS_FAILED, implode(', ', $failedIncrements));
                $this->_getSession()->addNotice($creationMessage . ' ' . $errorMessage);
                $dhlLogger->error($errorMessage);
            } else {
                $this->_getSession()->addNotice($creationMessage);
            }
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }

        $this->_redirect('adminhtml/sales_order');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/sales/order/actions/ship');
    }
}
