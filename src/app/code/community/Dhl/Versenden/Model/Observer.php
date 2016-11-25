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
class Dhl_Versenden_Model_Observer
{
    /**
     * Register autoloader in order to locate the extension libraries.
     */
    public function registerAutoload()
    {
        if (!Mage::getModel('dhl_versenden/config')->isAutoloadEnabled()) {
            return;
        }

        /** @var Dhl_Versenden_Helper_Autoloader $autoloader */
        $autoloader = Mage::helper('dhl_versenden/autoloader');
        $autoloader->addNamespace(
            "Psr\\", // prefix
            sprintf('%s/Dhl/Versenden/Psr/', Mage::getBaseDir('lib'))
        );
        $autoloader->addNamespace(
            "Dhl\\Versenden\\Bcs\\", // prefix
            sprintf('%s/Dhl/Versenden/Bcs/', Mage::getBaseDir('lib'))
        );

        $autoloader->register();
    }

    /**
     * Append the service selection form elements to the opc shipping method form.
     * - event: core_block_abstract_to_html_after
     *
     * @param Varien_Event_Observer $observer
     */
    public function appendServices(Varien_Event_Observer $observer)
    {
        $block = $observer->getData('block');
        if (!$block instanceof Mage_Checkout_Block_Onepage_Shipping_Method_Available) {
            return;
        }

        $serviceBlock = Mage::app()->getLayout()->createBlock(
            'dhl_versenden/checkout_onepage_shipping_method_service',
            'dhl_versenden_service',
            array(
                'template'    => 'dhl_versenden/checkout/shipping_services.phtml',
                'module_name' => 'Dhl_Versenden',
            )
        );

        $transport = $observer->getTransport();
        $html      = $transport->getHtml() . $serviceBlock->toHtml();
        $transport->setHtml($html);
    }

    /**
     * Append the service selection to the opc shipping method form in the progress side bar.
     * - event: core_block_abstract_to_html_after
     *
     * @param Varien_Event_Observer $observer
     */
    public function appendServicesToShippingMethod(Varien_Event_Observer $observer)
    {
        $block = $observer->getData('block');
        if (!$block instanceof Mage_Checkout_Block_Onepage_Progress
            || $block->getLayout()->getUpdate()->getHandles()[0] != 'checkout_onepage_progress_shipping_method'
        ) {
            return;
        }

        $versendenInfo = Mage::getSingleton('checkout/session')
                             ->getQuote()
                             ->getShippingAddress()
                             ->getData('dhl_versenden_info');

        if ($versendenInfo) {
            $serializer    = new \Dhl\Versenden\Bcs\Api\Info\Serializer();
            $versendenInfo = $serializer->unserialize($versendenInfo);
            $transport     = $observer->getData('transport');
            $transportHtml = trim($transport->getHtml());

            $block = Mage::app()
                         ->getLayout()
                         ->createBlock(
                             'dhl_versenden/config_service',
                             'dhl_services',
                             array('template' => 'dhl_versenden/config/services.phtml')
                         );
            $block->setData('services', $versendenInfo->getServices());

            $html = str_replace('</dd>', $block->toHtml() . '</dd>', $transportHtml);
            $transport->setHtml($html);
        }
    }

    /**
     * When the customer submits shipping method in OPC, then
     * - persist service settings
     * - process shipping address
     * Event:
     * - checkout_controller_onepage_save_shipping_method
     *
     * @param Varien_Event_Observer $observer
     */
    public function saveShippingSettings(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Quote $quote */
        $quote           = $observer->getQuote();
        $shippingAddress = $quote->getShippingAddress();

        $shipmentConfig = Mage::getModel('dhl_versenden/config_shipment');
        if (!$shipmentConfig->canProcessMethod($shippingAddress->getShippingMethod())) {
            // customer selected a shipping method not to be processed via DHL Versenden
            return;
        }

        /** @var Mage_Core_Controller_Request_Http $request */
        $request       = $observer->getRequest();
        $infoBuilder   = Mage::getModel('dhl_versenden/info_builder');
        $serviceInfo   = array(
            'shipment_service' => $request->getPost('shipment_service', array()),
            'service_setting'  => $request->getPost('service_setting', array()),
        );
        $versendenInfo = $infoBuilder->infoFromSales($shippingAddress, $serviceInfo, $quote->getStoreId());

        $shippingAddress->setData('dhl_versenden_info', $versendenInfo);
    }

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
            throw new Mage_Core_Exception($response->getStatus()->getStatusText());
        }

        $track->getShipment()->setShippingLabel(null);
        $track->getShipment()->save();
    }

    /**
     * Convert Info object to serialized representation.
     * - event: model_save_before
     *
     * @param Varien_Event_Observer $observer
     */
    public function serializeVersendenInfo(Varien_Event_Observer $observer)
    {
        $address = $observer->getData('object');
        if (!$address instanceof Mage_Customer_Model_Address_Abstract) {
            return;
        }

        $info = $address->getData('dhl_versenden_info');
        if (!$info || !$info instanceof \Dhl\Versenden\Bcs\Api\Info) {
            return;
        }

        $serializer = new \Dhl\Versenden\Bcs\Api\Info\Serializer();
        $address->setData('dhl_versenden_info', $serializer->serialize($info));
    }

    /**
     * Convert serialized info to Info object.
     * - event: model_load_after
     * - event: model_save_after
     *
     * @param Varien_Event_Observer $observer
     */
    public function unserializeVersendenInfo(Varien_Event_Observer $observer)
    {
        $address = $observer->getData('object');
        if (!$address instanceof Mage_Customer_Model_Address_Abstract) {
            return;
        }

        $info = $address->getData('dhl_versenden_info');
        if (!$info || !is_string($info)) {
            return;
        }

        $serializer = new \Dhl\Versenden\Bcs\Api\Info\Serializer();
        $address->setData('dhl_versenden_info', $serializer->unserialize($info));
    }

    /**
     * Convert serialized info to Info object.
     * - event: sales_order_address_collection_load_after
     *
     * @param Varien_Event_Observer $observer
     */
    public function unserializeVersendenInfoItems(Varien_Event_Observer $observer)
    {
        $collection = $observer->getData('order_address_collection');
        if (!$collection instanceof Mage_Sales_Model_Resource_Order_Address_Collection
            && !$collection instanceof Mage_Sales_Model_Resource_Quote_Address_Collection
        ) {
            return;
        }

        $unserializeInfo = function(Mage_Customer_Model_Address_Abstract $address) {
            $info = $address->getData('dhl_versenden_info');
            if (!$info || !is_string($info)) {
                return;
            }

            $serializer = new \Dhl\Versenden\Bcs\Api\Info\Serializer();
            $address->setData('dhl_versenden_info', $serializer->unserialize($info));
        };

        $collection->walk($unserializeInfo);
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
}
