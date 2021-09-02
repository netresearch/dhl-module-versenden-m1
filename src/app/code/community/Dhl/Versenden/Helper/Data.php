<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Get the currently installed Dhl_Versenden version.
     *
     * @return string
     */
    public function getModuleVersion()
    {
        $moduleName = $this->_getModuleName();
        return (string)Mage::getConfig()->getModuleConfig($moduleName)->version;
    }

    /**
     * Convert a timestamp to a CE(S)T time string.
     *
     * @param string $timestamp The timestamp to convert
     * @param string $format The output format
     * @return string
     */
    public function utcToCet($timestamp = null, $format = 'Y-m-d H:i:s')
    {
        if (null === $timestamp) {
            $timestamp = time();
        }

        $date = new DateTime("@$timestamp");
        $timezoneCet = new DateTimeZone('Europe/Berlin');

        $intervalSpec = sprintf("PT%dS", $timezoneCet->getOffset($date));
        $date->add(new DateInterval($intervalSpec));

        return $date->format($format);
    }

    /**
     * Check if customs data must be collected for export documents.
     *
     * @param string $shipperCountry
     * @param string $recipientCountry
     * @param string $recipientPostalCode
     * @return bool
     */
    public function isCollectCustomsData($shipperCountry, $recipientCountry, $recipientPostalCode)
    {
        $dutiableRoutes = [
            '^27498$', // DE - Helgoland
            '^78266$', // DE - Büsingen
            '^51\d{3}$', // ES - Ceuta
            '^52\d{3}$', // ES - Melilla
            '^3[58]\d{3}$', // ES - Canary Islands
            '^22060$' // IT - Campione d'Italia
        ];

        $nonDutiableRoutes = ['^[bB][tT][1-9][0-9]?\s[\w^_]{3}$']; // Northern Ireland

        $pattern = implode('|', $nonDutiableRoutes);
        if ($pattern && preg_match("/$pattern/", $recipientPostalCode)) {
            // given postal code matches a non-dutiable destination area
            return false;
        }

        $pattern = implode('|', $dutiableRoutes);
        if ($pattern && preg_match("/$pattern/", $recipientPostalCode)) {
            // given postal code matches a dutiable destination area
            return true;
        }

        // are shipper and receiver located in different countries?
        $diffCountry = ($shipperCountry != $recipientCountry);

        // are shipper and receiver both located in EU country?
        $bothEu = Mage::helper('core/data')->isCountryInEU($shipperCountry)
            && Mage::helper('core/data')->isCountryInEU($recipientCountry);


        return $diffCountry && !$bothEu;
    }

    /**
     * Get template name for packaging popup.
     *
     * @param string $template dhl template
     * @param string $block block name
     * @return string
     */
    public function getPackagingPopupTemplate($template, $block)
    {
        /** @var Mage_Adminhtml_Block_Sales_Order_Shipment_Packaging $blockObject */
        $blockObject = Mage::getSingleton('core/layout')->getBlock($block);
        $shippingMethod = $blockObject->getShipment()->getOrder()->getShippingMethod(true);

        if ($shippingMethod->getData('carrier_code') !== Dhl_Versenden_Model_Shipping_Carrier_Versenden::CODE) {
            // different carrier, return standard template
            return $blockObject->getTemplate();
        }

        return $template;
    }

    /**
     * Get template name for packaging packed info.
     *
     * @param string $template dhl template
     * @param string $block block name
     * @return string
     */
    public function getPackagingPackedTemplate($template, $block)
    {
        /** @var Mage_Adminhtml_Block_Sales_Order_Shipment_Packaging $blockObject */
        $blockObject = Mage::getSingleton('core/layout')->getBlock($block);
        $shippingMethod = $blockObject->getShipment()->getOrder()->getShippingMethod(true);

        if ($shippingMethod->getData('carrier_code') !== Dhl_Versenden_Model_Shipping_Carrier_Versenden::CODE) {
            // different carrier, return standard template
            return $blockObject->getTemplate();
        }

        return $template;
    }

    /**
     * Check if the given address implies delivery to a postal facility.
     *
     * @param Mage_Sales_Model_Quote_Address|Mage_Sales_Model_Order_Address $address
     * @return bool
     */
    public function isPostalFacility(Mage_Customer_Model_Address_Abstract $address)
    {
        // let 3rd party extensions add postal facility data
        $facility = new Varien_Object();

        Mage::dispatchEvent(
            'dhl_versenden_fetch_postal_facility', array(
                'customer_address'   => $address,
                'postal_facility' => $facility,
            )
        );

        return ($facility->getData('shop_type') !== null);
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @param string $message
     */
    public function addStatusHistoryComment(Mage_Sales_Model_Order $order, $message)
    {
        $history = Mage::getModel('sales/order_status_history')
            ->setOrder($order)
            ->setStatus($order->getStatus())
            ->setComment($message)
            ->setData('entity_name', Mage_Sales_Model_Order::HISTORY_ENTITY_NAME);

        $historyCollection = $order->getStatusHistoryCollection();
        $historyCollection->addItem($history);
        $historyCollection->save();
    }
}
