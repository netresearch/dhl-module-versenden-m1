<?php

/**
 * See LICENSE.md for license details.
 */

use Dhl\Versenden\ParcelDe\Info\Receiver\PostalFacility;

class Dhl_Versenden_Model_Observer extends Dhl_Versenden_Model_Observer_AbstractObserver
{
    /**
     * When a new order is placed, set the DHL Versenden carrier if applicable.
     * Event:
     * - sales_order_place_after
     *
     * @param Varien_Event_Observer $observer
     */
    public function updateCarrier(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Order $order */
        $order          = $observer->getData('order');
        $shippingMethod = $order->getShippingMethod();
        /** @var Dhl_Versenden_Model_Config_Shipment $config */
        $config = Mage::getModel('dhl_versenden/config_shipment');
        $shipperCountry = $config->getShipperCountry($order->getStoreId());

        if ($config->canProcessMethod($shippingMethod, $order->getStoreId())) {
            $parts          = explode('_', $shippingMethod);
            $parts[0]       = Dhl_Versenden_Model_Shipping_Carrier_Versenden::CODE;
            $shippingMethod = implode('_', $parts);
            $order->setShippingMethod($shippingMethod);
        }
    }

    /**
     * Read postal facility data from quote address.
     *
     * Dhl_Versenden comes with very basic facilities support, check out the
     * separate Dhl_LocationFinder extension for better integration.
     *
     * Facility properties:
     * - shop_type: [packStation|postOffice|parcelShop]
     * - shop_number: int(3)
     * - post_number: text(10)
     *
     * Event:
     * - dhl_versenden_fetch_postal_facility
     *
     * @param Varien_Event_Observer $observer
     */
    public function preparePostalFacility(Varien_Event_Observer $observer)
    {
        /** @var Varien_Object $facility */
        $facility = $observer->getPostalFacility();
        if ($facility->hasData()) {
            // someone else set a facility, we assume they know what they did.
            return;
        }

        /** @var Mage_Sales_Model_Quote_Address|Mage_Sales_Model_Order_Address $address */
        $address    = $observer->getCustomerAddress();
        $station    = $address->getStreetFull();
        $postNumber = $address->getCompany();

        if ($postNumber != '' && !is_numeric($postNumber)) {
            // not a valid DHL account number
            return;
        }

        // PHP 8.1: stripos() requires non-null string
        if ($station === null || $station === '') {
            return;
        }

        if (stripos($station, 'Packstation') === 0) {
            $facility->setData(
                [
                    'shop_type'   => PostalFacility::TYPE_PACKSTATION,
                    'shop_number' => preg_filter('/^.*([\d]{3})($|\n.*)/', '$1', $station),
                    'post_number' => $postNumber,
                ],
            );
        } elseif (stripos($station, 'Postfiliale') === 0) {
            $facility->setData(
                [
                    'shop_type'   => PostalFacility::TYPE_POSTFILIALE,
                    'shop_number' => preg_filter('/^.*([\d]{3})($|\n.*)/', '$1', $station),
                    'post_number' => $postNumber,
                ],
            );
        }
    }

    /**
     * Disable COD in case it is not available for the current destination.
     * - event: payment_method_is_active
     *
     * @param Varien_Event_Observer $observer
     */
    public function disableCodPayment(Varien_Event_Observer $observer)
    {
        $checkResult = $observer->getData('result');
        if (!$checkResult->isAvailable) {
            // payment method not available anyway
            return;
        }

        /** @var Mage_Sales_Model_Quote $quote */
        $quote = $observer->getData('quote');
        if (!$quote) {
            $quote = Mage::getSingleton('checkout/session')->getQuote();
        }

        if (!$quote) {
            // no quote, cannot check whether cod is allowed or not.
            return;
        }

        /** @var Mage_Payment_Model_Method_Abstract $methodInstance */
        $methodInstance = $observer->getData('method_instance');

        $shippingMethod = $quote->getShippingAddress()->getShippingMethod();
        $paymentMethod  = $methodInstance->getCode();

        /** @var Dhl_Versenden_Model_Config_Shipment $config */
        $config = Mage::getModel('dhl_versenden/config_shipment');
        if (!$config->canProcessMethod($shippingMethod, $quote->getStoreId())) {
            // no dhl shipping method
            return;
        }

        if (!$config->isCodPaymentMethod($paymentMethod, $quote->getStoreId())) {
            // no cod payment method
            return;
        }

        // obtain possible dhl products (national, weltpaket, …) and check if the filter allows cod for them
        $shipperCountry = Mage::getStoreConfig(
            Mage_Shipping_Model_Shipping::XML_PATH_STORE_COUNTRY_ID,
            $quote->getStoreId(),
        );
        $recipientCountry = $quote->getShippingAddress()->getCountryId();
        $euCountries = Mage::getStoreConfig(Mage_Core_Helper_Data::XML_PATH_EU_COUNTRIES_LIST, $quote->getStoreId());
        $euCountries = explode(',', $euCountries);

        $availableProducts = \Dhl\Versenden\ParcelDe\Product::getCodesByCountry(
            $shipperCountry,
            $recipientCountry,
            $euCountries,
        );

        $filter = new \Dhl\Versenden\ParcelDe\Service\Filter($availableProducts, false, false);
        $codService = $filter->filterService(new \Dhl\Versenden\ParcelDe\Service\Cod('cod', true, true, ''));
        if ($codService === null) {
            $checkResult->isAvailable = false;
        }
    }

    /**
     * Cancel the shipping label via DHL Business Customer Shipping API.
     * The track will not be deleted if shipping label deletion fails.
     * - event: sales_order_shipment_track_delete_before
     *
     * @param Varien_Event_Observer $observer
     *
     * @throws Mage_Core_Exception
     */
    public function deleteShippingLabel(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Order_Shipment_Track $track */
        $track = $observer->getData('track');
        if ($track->getCarrierCode() !== Dhl_Versenden_Model_Shipping_Carrier_Versenden::CODE) {
            // some other carrier, not our business.
            return;
        }

        if (!$track->getShipment()->hasShippingLabel()) {
            // shipment has no label, no need to send cancellation request
            return;
        }

        try {
            // REST CLIENT - replaces SOAP gateway
            /** @var Dhl_Versenden_Model_Webservice_Client_Shipment $client */
            $client = Mage::getModel('dhl_versenden/webservice_client_shipment');

            $shipmentNumber = $track->getData('track_number');
            $canceledNumbers = $client->cancelShipments([$shipmentNumber]);

            // Success - log it (event dispatch handled by client, not observer)
            Mage::log(
                sprintf('Canceled DHL shipment: %s', $shipmentNumber),
                Zend_Log::INFO,
                'dhl_versenden.log',
            );

        } catch (\Dhl\Sdk\ParcelDe\Shipping\Exception\DetailedServiceException $e) {
            // Detailed error (e.g., shipment not found, already cancelled)
            Mage::throwException($e->getMessage());

        } catch (\Dhl\Sdk\ParcelDe\Shipping\Exception\ServiceException $e) {
            // Generic API error
            Mage::throwException('Shipment deletion failed: ' . $e->getMessage());
        }

        $track->getShipment()->setShippingLabel(null);
        $track->getShipment()->save();
    }



    /**
     * Override form block as defined via container properties when additional
     * DHL Versenden address fields need to be displayed.
     * - event: adminhtml_widget_container_html_before
     *
     * @param Varien_Event_Observer $observer
     */
    public function replaceAddressForm(Varien_Event_Observer $observer)
    {
        $container = $observer->getData('block');
        if (!$container instanceof Mage_Adminhtml_Block_Sales_Order_Address) {
            return;
        }

        $address = Mage::registry('order_address');
        if (!$address || ($address->getAddressType() !== Mage_Customer_Model_Address_Abstract::TYPE_SHIPPING)) {
            return;
        }

        $shippingMethod = $address->getOrder()->getShippingMethod(true);
        if ($shippingMethod->getData('carrier_code') !== Dhl_Versenden_Model_Shipping_Carrier_Versenden::CODE) {
            return;
        }

        $info = $address->getData('dhl_versenden_info');
        if (!$info instanceof \Dhl\Versenden\ParcelDe\Info) {
            return;
        }

        $origAddressForm = $container->getChild('form');
        if (!$origAddressForm instanceof Mage_Adminhtml_Block_Sales_Order_Create_Form_Address) {
            return;
        }

        $dhlAddressForm = Mage::app()->getLayout()->getBlock('dhl_versenden_form');
        $dhlAddressForm->setDisplayVatValidationButton($origAddressForm->getDisplayVatValidationButton());
        $container->setChild('form', $dhlAddressForm);
    }

    /**
     * Set service configuration flags to the head block in checkout.
     *
     * Some JS validation scripts in checkout are included dynamically,
     * depending on whether or not preferred services are enabled in config.
     * Adding scripts dynamically is realised via flags at the head block,
     * which get evaluated during block rendering.
     *
     * - event: controller_action_layout_render_before_checkout_onepage_index
     *
     * @see \Mage_Page_Block_Html_Head::getCssJsHtml
     */
    public function setShippingServiceFlags()
    {
        /** @var Dhl_Versenden_Helper_Service $serviceHelper */
        $serviceHelper = Mage::helper('dhl_versenden/service');

        if ($serviceHelper->isLocationAndNeighbourEnabled()) {
            $headBlock = Mage::app()->getLayout()->getBlock('head');
            $headBlock->setData(Dhl_Versenden_Helper_Service::PREFERRED_SERVICE_ALL_ENABLED, true);
        }

        if ($serviceHelper->isLocationOrNeighbourEnabled()) {
            $headBlock = Mage::app()->getLayout()->getBlock('head');
            $headBlock->setData(Dhl_Versenden_Helper_Service::PREFERRED_SERVICE_ANY_ENABLED, true);
        }
    }

    /**
     * Add Service fee fo shipping costs.
     *
     * @param Varien_Event_Observer $observer
     *
     * @return void
     * - event: sales_quote_collect_totals_before
     */
    public function addServiceFee(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Quote $quote */
        $quote = $observer->getQuote();
        $shippingAddress = $quote->getShippingAddress();
        $shippingMethod  = $shippingAddress->getShippingMethod();

        /** @var \Dhl\Versenden\ParcelDe\Info $dhlVersendenInfo */
        $dhlVersendenInfo = $shippingAddress->getData('dhl_versenden_info');

        /** @var Dhl_Versenden_Model_Config_Shipment $config */
        $config = Mage::getModel('dhl_versenden/config_shipment');
        if (!$config->canProcessMethod($shippingMethod, $quote->getStoreId())) {
            $dhlVersendenInfo = null;
            $this->resetVersendenInfo($observer);
        }

        if ($dhlVersendenInfo === null) {
            return;
        }

        if (!$dhlVersendenInfo instanceof \Dhl\Versenden\ParcelDe\Info) {
            $serializer = new \Dhl\Versenden\ParcelDe\Info\Serializer();
            $dhlVersendenInfo = $serializer->unserialize($dhlVersendenInfo);
        }

        $services = $dhlVersendenInfo->getServices();
        $hasFeeService = $services->preferredDay
            || $services->closestDropPoint || $services->noNeighbourDelivery || $services->goGreen;
        if ($hasFeeService) {
            $store = Mage::app()->getStore($quote->getStoreId());
            /** @var Dhl_Versenden_Model_Config_Service $config */
            $config = Mage::getModel('dhl_versenden/config_service');
            $prefDayHandlingFee = $services->preferredDay ? $config->getPrefDayFee($store->getId()) : 0;
            $cdpFee = $services->closestDropPoint ? $config->getCdpFee($store->getId()) : 0;
            $noNeighbourFee = $services->noNeighbourDelivery ? $config->getNoNeighbourDeliveryFee($store->getId()) : 0;
            $goGreenFee = $services->goGreen ? $config->getGoGreenFee($store->getId()) : 0;
            $totalServiceFee = $prefDayHandlingFee + $cdpFee + $noNeighbourFee + $goGreenFee;

            list($carrierCode, $method) = explode('_', $shippingMethod, 2);

            $initialPrice = $store->getConfig("carriers/{$carrierCode}/price");
            $initialFeeType = $store->getConfig("carriers/{$carrierCode}/handling_type");
            $initialFee = (float) $store->getConfig("carriers/{$carrierCode}/handling_fee");

            if ($initialFeeType === Mage_Shipping_Model_Carrier_Abstract::HANDLING_TYPE_FIXED) {
                $handlingFee = $totalServiceFee + $initialFee;
            } elseif ($initialFeeType === Mage_Shipping_Model_Carrier_Abstract::HANDLING_TYPE_PERCENT) {
                $initialFixedFee = ($initialFee / 100) * $initialPrice;
                $handlingFee = $initialFixedFee + $totalServiceFee;
            } else {
                $handlingFee = $totalServiceFee;
            }

            /**
             * Add handling fee , F stands for fixed.
             */
            $store->setConfig("carriers/{$carrierCode}/handling_type", 'F');
            $store->setConfig("carriers/{$carrierCode}/handling_fee", $handlingFee);
        }

        // needed to re collect shipping incl. fee in all steps
        $quote->getShippingAddress()->setCollectShippingRates(true);
    }

    /**
     * Reset the dhl versenden info when jumping back in checkout steps.
     * - event: controller_action_predispatch_checkout_onepage_saveShipping
     * - event: controller_action_predispatch_checkout_onepage_saveBilling
     *
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function resetVersendenInfo(Varien_Event_Observer $observer)
    {
        /** @var Mage_Checkout_Model_Session $session */
        $session = Mage::getSingleton('checkout/session');
        /** @var Mage_Sales_Model_Quote $quote */
        $quote = Mage::getModel('sales/quote')->load($session->getQuoteId());
        $shippingAddress = $quote->getShippingAddress();
        $shippingAddress->setData('dhl_versenden_info', null);
        $shippingAddress->save();

        return $this;
    }

    /**
     * Add "Create Shipping Labels" mass action to order grid.
     * - event: adminhtml_block_html_before
     *
     * @param Varien_Event_Observer $observer
     */
    public function addAutocreateMassAction(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();

        if (!$block instanceof Mage_Adminhtml_Block_Widget_Grid_Massaction) {
            // not a mass action block at all
            return;
        }

        if ($block->getRequest()->getControllerName() !== 'sales_order') {
            // not an order grid mass action block
            return;
        }

        $itemData = [
            'label' => Mage::helper('dhl_versenden/data')->__('Create Shipping Labels'),
            'url' => $block->getUrl('adminhtml/sales_order_autocreate/massCreateShipmentLabel'),
        ];

        $block->addItem('dhlversenden_label_create', $itemData);
    }
}
