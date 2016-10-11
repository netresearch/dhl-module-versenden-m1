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
 * Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Customs
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Customs
    extends Mage_Core_Block_Template
{
    /**
     * Retrieve shipment model instance
     *
     * @return Mage_Sales_Model_Order_Shipment
     */
    public function getShipment()
    {
        return Mage::registry('current_shipment');
    }

    /**
     * @return mixed[]
     */
    public function getTermsOfTrade()
    {
        $carrierTerms = Mage::getSingleton('dhl_versenden/shipping_carrier_versenden')->getCode('terms_of_trade');

        $terms = array(
            array('value' => '', 'label' => $this->helper('adminhtml')->__('--Please Select--')),
        );

        foreach ($carrierTerms as $carrierTerm) {
            $terms[]= array('value' => $carrierTerm, 'label' => $carrierTerm);
        }

        return $terms;
    }

    /**
     * Get Currency Code for Custom Value
     *
     * @return string
     */
    public function getCustomValueCurrencyCode()
    {
        $orderInfo = $this->getShipment()->getOrder();
        return $orderInfo->getBaseCurrency()->getCurrencyCode();
    }
}
