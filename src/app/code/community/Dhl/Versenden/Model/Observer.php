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
use Dhl\Versenden\ShippingInfo;
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

        $autoloader = Mage::helper('dhl_versenden/autoloader');

        $dhlLibs = ['Versenden', 'Gkp'];
        array_walk($dhlLibs, function ($libDir) use ($autoloader) {
            $autoloader->addNamespace(
                "Dhl\\$libDir\\", // prefix
                sprintf('%s/Dhl/%s/', Mage::getBaseDir('lib'), $libDir) // baseDir
            );
        });

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
        $block = $observer->getBlock();
        if (!$block instanceof Mage_Checkout_Block_Onepage_Shipping_Method_Available) {
            return;
        }

        $serviceBlock = Mage::app()->getLayout()->createBlock(
            'dhl_versenden/checkout_onepage_shipping_method_service',
            'dhl_versenden_service',
            [
                'template' => 'dhl_versenden/checkout/onepage/shipping_method/service.phtml',
                'module_name' => 'Dhl_Versenden',
            ]
        );

        $transport = $observer->getTransport();
        $html = $transport->getHtml() . $serviceBlock->toHtml();
        $transport->setHtml($html);
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
        $quote = $observer->getQuote();

        $shippingAddress = $quote->getShippingAddress();
        $enabledMethods = Mage::getModel('dhl_versenden/config')->getShipmentSettings()->shippingMethods;
        if (!in_array($shippingAddress->getShippingMethod(), $enabledMethods)) {
            // customer selected a shipping method not to be processed via DHL Versenden
            return;
        }

        /** @var Mage_Core_Controller_Request_Http $request */
        $request = $observer->getRequest();

        $serviceSettings = Mage::helper('dhl_versenden/data')->getServiceSettings(
            $request->getPost('shipment_service', []),
            $request->getPost('service_setting', [])
        );
        $receiver = Mage::helper('dhl_versenden/data')->getReceiver($shippingAddress);

        $shippingInfo = new ShippingInfo($serviceSettings, $receiver);
        $shippingAddress->setDhlVersendenInfo($shippingInfo->getJson());
    }
}
