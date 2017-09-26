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
use \Dhl\Versenden\Bcs\Api\Webservice;
use \Dhl\Versenden\Bcs\Api\Product;
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
    const PACKAGE_MIN_WEIGHT = 0.01;

    const EXPORT_TYPE_COMMERCIAL_SAMPLE = 'COMMERCIAL_SAMPLE';
    const EXPORT_TYPE_DOCUMENT          = 'DOCUMENT';
    const EXPORT_TYPE_OTHER             = 'OTHER';
    const EXPORT_TYPE_PRESENT           = 'PRESENT';
    const EXPORT_TYPE_RETURN_OF_GOODS   = 'RETURN_OF_GOODS';

    const TOT_DDP = 'DDP';
    const TOT_DXV = 'DXV';
    const TOT_DDU = 'DDU';
    const TOT_DDX = 'DDX';

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
    public function getProducts($shipperCountry, $recipientCountry)
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
     * Return content types of package
     *
     * @param Varien_Object $params
     * @return array
     */
    public function getContentTypes(Varien_Object $params)
    {
        $contentTypes = array();

        $isInternational = Mage::helper('dhl_versenden/data')->isCollectCustomsData(
            $params->getData('country_shipper'),
            $params->getData('country_recipient')
        );

        if ($isInternational) {
            $contentTypes = array(
                self::EXPORT_TYPE_COMMERCIAL_SAMPLE => Mage::helper('dhl_versenden/data')->__('Commercial Sample'),
                self::EXPORT_TYPE_DOCUMENT => Mage::helper('dhl_versenden/data')->__('Document'),
                self::EXPORT_TYPE_OTHER => Mage::helper('dhl_versenden/data')->__('Other'),
                self::EXPORT_TYPE_PRESENT => Mage::helper('dhl_versenden/data')->__('Present'),
                self::EXPORT_TYPE_RETURN_OF_GOODS => Mage::helper('dhl_versenden/data')->__('Return Of Goods'),
            );
        }

        return $contentTypes;
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

        // add selected services to request
        $serviceData = array(
            'shipment_service' => $httpRequest->getPost('shipment_service', array()),
            'service_setting'  => $httpRequest->getPost('service_setting', array()),
        );
        $request->setData('services', $serviceData);

        // add dhl product to request
        $shipperCountry = Mage::getModel('dhl_versenden/config')
            ->getShipperCountry($request->getOrderShipment()->getStoreId());
        $recipientCountry = $request->getOrderShipment()->getShippingAddress()->getCountryId();
        $products = $this->getProducts($shipperCountry, $recipientCountry);
        $productCodes = array_keys($products);
        $request->setData('gk_api_product', $productCodes[0]);

        // add customs information to request
        $request->setData('customs', $httpRequest->getPost('customs', array()));

        $sequenceNumber = 0;
        $shipmentRequests = array(
            $sequenceNumber => $request,
        );

        $response = new Varien_Object();

        try {
            $result = Mage::getModel('dhl_versenden/webservice_gateway_soap')
                ->createShipmentOrder($shipmentRequests);

            // collect validation errors (occurred before api request)
            $shipmentOrderErrors = array();
            foreach ($shipmentRequests as $shipmentRequest) {
                if ($shipmentRequest->hasData('request_data_exception')) {
                    $shipmentOrderErrors[]= sprintf(
                        '#%s: %s',
                        $shipmentRequest->getOrderShipment()->getOrder()->getIncrementId(),
                        $shipmentRequest->getData('request_data_exception')
                    );
                }
            }

            if (!empty($shipmentOrderErrors) || empty($result)) {
                $msg = sprintf('%s %s', 'The shipment request(s) had errors.', implode("\n", $shipmentOrderErrors));
                throw new Webservice\RequestData\ValidationException($msg);
            }

            // collect response errors (occurred during api request)
            $shipmentStatus = $result->getCreatedItems()->getItem($sequenceNumber)->getStatus();
            if ($shipmentStatus->isError()) {
                throw new Webservice\ResponseData\Status\Exception($shipmentStatus);
            }

            // if no request or response exceptions occurred, read label data
            $pdfLib = new \Dhl\Versenden\Bcs\Api\Pdf\Adapter\Zend();
            $responseData = array(
                'info' => array(array(
                    'tracking_number' => $result->getShipmentNumber($sequenceNumber),
                    'label_content'   => $result->getCreatedItems()->getItem($sequenceNumber)->getAllLabels($pdfLib),
                ))
            );
            $response->setData($responseData);
        } catch (Webservice\Exception $e) {
            // convert to Mage_Core_Exception for proper message display in frontend
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
            'terms_of_trade' => array(
                self::TOT_DDP => self::TOT_DDP,
                self::TOT_DXV => self::TOT_DXV,
                self::TOT_DDU => self::TOT_DDU,
                self::TOT_DDX => self::TOT_DDX,
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

    /**
     * @param string $tracking
     * @return false|Mage_Core_Model_Abstract
     */
    public function getTrackingInfo($tracking)
    {
        $trackData = array(
            'carrier' => $this->_code,
            'carrier_title' => $this->getConfigData('title'),
            'tracking' => $tracking,
            'url' => 'http://nolp.dhl.de/nextt-online-public/set_identcodes.do?lang=de&idc=' . $tracking
        );

        return Mage::getModel('shipping/tracking_result_status', $trackData);
    }
}
