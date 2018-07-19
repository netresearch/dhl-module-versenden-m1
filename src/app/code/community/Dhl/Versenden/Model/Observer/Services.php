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
 * @author    Max Melzer <max.melzer@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */

/**
 * Dhl_Versenden_Model_Observer_Services
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Model_Observer_Services extends Dhl_Versenden_Model_Observer_AbstractObserver
{

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

        /** @var Dhl_Versenden_Model_Config $configModel */
        $configModel = Mage::getModel('dhl_versenden/config');
        if (!$configModel->isActive()) {
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

        // Set the billing address mail address as fallback if the shipping address has none
        if (!$shippingAddress->getData('email')) {
            $shippingAddress->setData('email', $quote->getBillingAddress()->getData('email'));
        }

        $versendenInfo = $infoBuilder->infoFromSales($shippingAddress, $serviceInfo, $quote->getStoreId());

        $shippingAddress->setData('dhl_versenden_info', $versendenInfo);
    }

    /**
     * @param Varien_Event_Observer $observer
     * @throws Exception
     */
    public function validateLocationDetails(Varien_Event_Observer $observer)
    {
        $requests = $observer->getEvent()->getData('shipment_requests');
        foreach ($requests as $requset) {
            /**
             * @var Mage_Shipping_Model_Shipment_Request
             */
            $services = $requset->getData('services');
            $serviceSettings = $services['service_setting'];
            $keys = array(
                \Dhl\Versenden\Bcs\Api\Shipment\Service\PreferredLocation::CODE,
                \Dhl\Versenden\Bcs\Api\Shipment\Service\PreferredNeighbour::CODE
            );
        }

        foreach ($keys as $key) {
            if (array_key_exists($key, $serviceSettings)) {
                $this->checkValue($serviceSettings[$key], $key);
            }
        }
    }

    /**
     * @param $value
     * @param $key
     * @throws Exception
     */
    public function checkValue($value, $key)
    {
        $pattern = '/\bPaketbox|\bPackstation|\bPostfach|\bPostfiliale|\bFiliale|\bPostfiliale Direkt|'.'
                    \bFiliale Direkt|\bPaketkasten|\bDHL|\bP-A-C-K-S-T-A-T-I-O-N|\bPaketstation|\bPack Station|'.'
                    \bP.A.C.K.S.T.A.T.I.O.N.|\bPakcstation|\bPaackstation|\bPakstation|\bBackstation|\bBakstation|'.'
                    \bP A C K S T A T I O N|\bWunschfiliale|\bDeutsche Post/';
        $patternSpec = "/[+[\]\'\;,.\/{}|\":<>?~\\\\]/";
        preg_match($pattern, $value, $matchWords);
        preg_match($patternSpec, $value, $matchSpecialChars);

        if (!empty($matchWords) || !empty($matchSpecialChars)) {
            $hint = ucfirst(strtolower(preg_replace('/(?=[A-Z])/', '$1 $2', $key)));
            $msg = Mage::helper('dhl_versenden/data')->__($hint);
            $msg .= ': ' . Mage::helper('dhl_versenden/data')->__('Your input is invalid');
            Mage::throwException($msg);
        }
    }
}
