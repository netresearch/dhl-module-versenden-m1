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
use \Dhl\Versenden\Webservice;
use \Dhl\Versenden\Product;
/**
 * Dhl_Versenden_Model_Shipping_Carrier_Versenden
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Model_Shipping_Carrier_Versenden
    extends Mage_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{
    const CODE = 'dhlversenden';
    const PACKAGE_MIN_WEIGHT = 0.2;


    /**
     * Init carrier code
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_code = self::CODE;
    }

    /**
     * @param string $shipperCountry
     * @param string $recipientCountry
     * @return string[]
     */
    protected function getProducts($shipperCountry, $recipientCountry)
    {
        $products = array(
            Product::CODE_PAKET_NATIONAL => Mage::helper('dhl_versenden/data')->__('DHL Paket National'),
            Product::CODE_WELTPAKET => Mage::helper('dhl_versenden/data')->__('DHL Weltpaket'),
            Product::CODE_EUROPAKET => Mage::helper('dhl_versenden/data')->__('DHL Europaket'),
            Product::CODE_KURIER_TAGGLEICH => Mage::helper('dhl_versenden/data')->__('DHL Kurier Taggleich'),
            Product::CODE_KURIER_WUNSCHZEIT => Mage::helper('dhl_versenden/data')->__('DHL Kurier Wunschzeit'),

            Product::CODE_PAKET_AUSTRIA => Mage::helper('dhl_versenden/data')->__('DHL Paket Austria'),
            Product::CODE_PAKET_CONNECT => Mage::helper('dhl_versenden/data')->__('DHL PAKET Connect'),
            Product::CODE_PAKET_INTERNATIONAL => Mage::helper('dhl_versenden/data')->__('DHL PAKET International'),
        );

        if (!$shipperCountry) {
            // all translated products
            $productsCodes = Product::getCodes();
            $productsCodes = array_combine($productsCodes, $productsCodes);
            $shipmentCodes = array_intersect_key($products, $productsCodes);
        } else {
            // translated products for given shipper / recipient combination
            $euCountries = explode(',', Mage::getStoreConfig(Mage_Core_Helper_Data::XML_PATH_EU_COUNTRIES_LIST));

            $productsCodes = Product::getCodesByCountry($shipperCountry, $recipientCountry, $euCountries);
            $productsCodes = array_combine($productsCodes, $productsCodes);
            $shipmentCodes = array_intersect_key($products, $productsCodes);
        }

        return $shipmentCodes;
    }

    /**
     * The DHL Versenden carrier does not calculate rates.
     *
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return null
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        return null;
    }

    /**
     * The DHL Versenden carrier does not introduce own methods.
     *
     * @return mixed[]
     */
    public function getAllowedMethods()
    {
        return array();
    }

    /**
     * Check if carrier has shipping label option available
     *
     * @return boolean
     */
    public function isShippingLabelsAvailable()
    {
        return true;
    }

    /**
     * Return container types of carrier
     *
     * @param Varien_Object|null $params
     * @return string[]
     */
    public function getContainerTypes(Varien_Object $params = null)
    {
        if (!$params) {
            $countryShipper = null;
            $countryRecipient = null;
        } else {
            $countryShipper = $params->getData('country_shipper');
            $countryRecipient = $params->getData('country_recipient');
        }

        return $this->getProducts($countryShipper, $countryRecipient);
    }

    /**
     * Do request to shipment
     *
     * @param Mage_Shipping_Model_Shipment_Request $request
     * @return Varien_Object
     * @throws Exception
     */
    public function requestToShipment(Mage_Shipping_Model_Shipment_Request $request)
    {
        $httpRequest = Mage::app()->getFrontController()->getRequest();

        $serviceData = array(
            'shipment_service' => $httpRequest->getPost('shipment_service', array()),
            'service_setting'  => $httpRequest->getPost('service_setting', array()),
        );
        $request->setData('services', $serviceData);

        $sequenceNumber = 0;
        $shipmentRequests = array(
            $sequenceNumber => $request,
        );

        $response = new Varien_Object();

        try {
            $result = Mage::getModel('dhl_versenden/webservice_gateway_soap')
                ->createShipmentOrder($shipmentRequests);
            $shipmentNumber = $result->getShipmentNumber($sequenceNumber);

            $shipmentStatus = $result->getLabels()->getItem($shipmentNumber)->getStatus();
            if ($shipmentStatus->isError()) {
                throw new Webservice\ResponseData\StatusException($shipmentStatus);
            }

            $responseData = array(
                'info' => array(array(
                    'tracking_number' => $shipmentNumber,
                    'label_content'   => $result->getLabels()->getItem($shipmentNumber)->getLabel(),
                ))
            );
            $response->setData($responseData);
        } catch (Webservice\Exception $e) {
            Mage::throwException($e->getMessage());
        }

        return $response;
    }

    /**
     * @param string $type
     * @param string $code
     * @return bool|mixed
     */
    public function getCode($type, $code = '')
    {
        $codes = array(
            'unit_of_measure' => array(
                'G'   =>  Mage::helper('dhl_versenden')->__('Grams'),
                'KG'  =>  Mage::helper('dhl_versenden')->__('Kilograms'),
            ),
        );

        if (!isset($codes[$type])) {
            return false;
        } elseif ('' === $code) {
            return $codes[$type];
        }

        if (!isset($codes[$type][$code])) {
            return false;
        } else {
            return $codes[$type][$code];
        }
    }
}
