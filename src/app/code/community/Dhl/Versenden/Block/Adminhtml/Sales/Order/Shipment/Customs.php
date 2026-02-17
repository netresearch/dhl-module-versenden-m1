<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Block_Adminhtml_Sales_Order_Shipment_Customs extends Mage_Core_Block_Template
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

        $terms = [
            ['value' => '', 'label' => $this->helper('adminhtml')->__('--Please Select--')],
        ];

        foreach ($carrierTerms as $carrierTerm) {
            $terms[] = ['value' => $carrierTerm, 'label' => $carrierTerm];
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

    /**
     * @return float
     */
    public function getPostalCharges()
    {
        $order = $this->getShipment()->getOrder();
        return $order->getBaseShippingInclTax() - $order->getBaseShippingRefunded() - $order->getBaseShippingTaxRefunded();
    }
}
