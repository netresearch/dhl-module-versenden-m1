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
use Dhl\Versenden\Bcs\Api\Info\Receiver\PostalFacility;

/**
 * Dhl_Versenden_Model_Observer
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
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

        if (stripos($station, 'Packstation') === 0) {
            $facility->setData(
                array(
                    'shop_type'   => PostalFacility::TYPE_PACKSTATION,
                    'shop_number' => preg_filter('/^.*([\d]{3})($|\n.*)/', '$1', $station),
                    'post_number' => $postNumber,
                )
            );
        } elseif (stripos($station, 'Postfiliale') === 0) {
            $facility->setData(
                array(
                    'shop_type'   => PostalFacility::TYPE_POSTFILIALE,
                    'shop_number' => preg_filter('/^.*([\d]{3})($|\n.*)/', '$1', $station),
                    'post_number' => $postNumber,
                )
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

        // obtain possible dhl products (national, weltpaket, …) and check if
        // the filter allows cod for these them
        $shipperCountry   = Mage::getStoreConfig(
            Mage_Shipping_Model_Shipping::XML_PATH_STORE_COUNTRY_ID,
            $quote->getStoreId()
        );
        $recipientCountry = $quote->getShippingAddress()->getCountryId();
        $euCountries      =
            Mage::getStoreConfig(Mage_Core_Helper_Data::XML_PATH_EU_COUNTRIES_LIST, $quote->getStoreId());
        $euCountries      = explode(',', $euCountries);

        $availableProducts = \Dhl\Versenden\Bcs\Api\Product::getCodesByCountry(
            $shipperCountry,
            $recipientCountry,
            $euCountries
        );

        $filter     = new \Dhl\Versenden\Bcs\Api\Shipment\Service\Filter($availableProducts, false, false);
        $codService = $filter->filterService(new \Dhl\Versenden\Bcs\Api\Shipment\Service\Cod('cod', true, true));
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

        $gateway         = Mage::getModel('dhl_versenden/webservice_gateway_soap');
        $shipmentNumbers = array($track->getData('track_number'));
        $response        = $gateway->deleteShipmentOrder($shipmentNumbers);
        if ($response->getStatus()->isError()) {
            Mage::throwException($response->getStatus()->getStatusText());
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
        if (!$info instanceof \Dhl\Versenden\Bcs\Api\Info) {
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

        /** @var \Dhl\Versenden\Bcs\Api\Info $dhlVersendenInfo */
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

        if (!$dhlVersendenInfo instanceof \Dhl\Versenden\Bcs\Api\Info) {
            $serializer = new \Dhl\Versenden\Bcs\Api\Info\Serializer();
            $dhlVersendenInfo = $serializer->unserialize($dhlVersendenInfo);
        }

        $services = $dhlVersendenInfo->getServices();
        if ($services->preferredTime || $services->preferredDay) {
            $combined = $services->preferredTime && $services->preferredDay;
            $store = Mage::app()->getStore($quote->getStoreId());
            /** @var Dhl_Versenden_Model_Config_Service $config */
            $config = Mage::getModel('dhl_versenden/config_service');
            $prefTimeHandlingFee = $services->preferredTime ? $config->getPrefTimeFee($store->getId()) : 0;
            $prefDayHandlingFee = $services->preferredDay ? $config->getPrefDayFee($store->getId()) : 0;
            $prefDayAndTimeHandlingFee = $combined ? $config->getPrefDayAndTimeFee($store->getId()) : 0;

            list($carrierCode, $method) = explode('_', $shippingMethod, 2);

            $initialPrice = $store->getConfig("carriers/{$carrierCode}/price");
            $initialFeeType = $store->getConfig("carriers/{$carrierCode}/handling_type");
            $initialFee = $store->getConfig("carriers/{$carrierCode}/handling_fee");

            if ($initialFeeType === Mage_Shipping_Model_Carrier_Abstract::HANDLING_TYPE_FIXED) {
                $handlingFee = $combined ?
                    $prefDayAndTimeHandlingFee :
                    $prefDayHandlingFee + $prefTimeHandlingFee + $initialFee ;
            } elseif ($initialFeeType === Mage_Shipping_Model_Carrier_Abstract::HANDLING_TYPE_PERCENT) {
                $initialFixedFee = ($initialFee / 100) * $initialPrice;
                $handlingFee =  $combined ?
                    $initialFixedFee + $prefDayAndTimeHandlingFee :
                    $initialFixedFee + $prefDayHandlingFee + $prefTimeHandlingFee;
            } else {
                $handlingFee =  $combined ? $prefDayAndTimeHandlingFee : $prefDayHandlingFee + $prefTimeHandlingFee;
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

        $itemData = array(
            'label' => Mage::helper('dhl_versenden/data')->__('Create Shipping Labels'),
            'url' => $block->getUrl('adminhtml/sales_order_autocreate/massCreateShipmentLabel'),
        );

        $block->addItem('dhlversenden_label_create', $itemData);
    }
}
