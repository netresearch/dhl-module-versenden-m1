<?php

/**
 * See LICENSE.md for license details.
 */

use Dhl\Versenden\ParcelDe\Product;

class Dhl_Versenden_Model_Shipping_Carrier_Versenden extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface
{
    public const CODE = 'dhlversenden';
    public const PACKAGE_MIN_WEIGHT = 0.01;

    public const EXPORT_TYPE_COMMERCIAL_GOODS  = 'COMMERCIAL_GOODS';
    public const EXPORT_TYPE_COMMERCIAL_SAMPLE = 'COMMERCIAL_SAMPLE';
    public const EXPORT_TYPE_DOCUMENT          = 'DOCUMENT';
    public const EXPORT_TYPE_OTHER             = 'OTHER';
    public const EXPORT_TYPE_PRESENT           = 'PRESENT';
    public const EXPORT_TYPE_RETURN_OF_GOODS   = 'RETURN_OF_GOODS';

    public const TOT_DDP = 'DDP';
    public const TOT_DXV = 'DXV';
    public const TOT_DDU = 'DDU';
    public const TOT_DDX = 'DDX';

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
        $translator = Mage::helper('dhl_versenden/data');
        $products = [
            Product::CODE_PAKET_NATIONAL => $translator->__('DHL Paket National'),
            Product::CODE_KLEINPAKET => $translator->__('DHL Kleinpaket'),
            Product::CODE_WELTPAKET => $translator->__('DHL Weltpaket'),
            Product::CODE_EUROPAKET => $translator->__('DHL Europaket'),
            Product::CODE_WARENPOST_INTERNATIONAL => $translator->__('DHL Warenpost International'),
            Product::CODE_KURIER_TAGGLEICH => $translator->__('DHL Kurier Taggleich'),
            Product::CODE_KURIER_WUNSCHZEIT => $translator->__('DHL Kurier Wunschzeit'),
        ];

        if (!$shipperCountry) {
            // all translated products
            $productsCodes = Product::getCodes();
        } else {
            // translated products for given shipper / recipient combination
            $euCountries = explode(',', Mage::getStoreConfig(Mage_Core_Helper_Data::XML_PATH_EU_COUNTRIES_LIST));
            $productsCodes = Product::getCodesByCountry($shipperCountry, $recipientCountry, $euCountries);
        }

        $productsCodes = array_combine($productsCodes, $productsCodes);
        return array_intersect_key($products, $productsCodes);
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
        return [];
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
        $contentTypes = [];

        $shipment = Mage::registry('current_shipment');
        if (!$shipment) {
            $recipientPostalCode = '';
        } else {
            $recipientPostalCode = (string) $shipment->getOrder()->getShippingAddress()->getPostcode();
        }

        $isInternational = Mage::helper('dhl_versenden/data')->isCollectCustomsData(
            $params->getData('country_shipper'),
            $params->getData('country_recipient'),
            $recipientPostalCode,
        );

        if ($isInternational) {
            $contentTypes = [
                self::EXPORT_TYPE_COMMERCIAL_GOODS => Mage::helper('dhl_versenden/data')->__('Commercial Goods'),
                self::EXPORT_TYPE_COMMERCIAL_SAMPLE => Mage::helper('dhl_versenden/data')->__('Commercial Sample'),
                self::EXPORT_TYPE_DOCUMENT => Mage::helper('dhl_versenden/data')->__('Document'),
                self::EXPORT_TYPE_PRESENT => Mage::helper('dhl_versenden/data')->__('Present'),
                self::EXPORT_TYPE_RETURN_OF_GOODS => Mage::helper('dhl_versenden/data')->__('Return Of Goods'),
                self::EXPORT_TYPE_OTHER => Mage::helper('dhl_versenden/data')->__('Other'),
            ];
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
        $serviceData = [
            'shipment_service' => $httpRequest->getPost('shipment_service', []),
            'service_setting'  => $httpRequest->getPost('service_setting', []),
        ];
        $request->setData('services', $serviceData);

        // add dhl product to request
        $product = $httpRequest->getPost('shipping_product');
        if (!$product) {
            // Check if product was already set on the request (e.g., in tests)
            $product = $request->getData('gk_api_product');
        }
        if (!$product) {
            $storeId = $request->getOrderShipment()->getStoreId();
            $shipperCountry = Mage::getModel('dhl_versenden/config')->getShipperCountry($storeId);
            $recipientCountry = $request->getOrderShipment()->getShippingAddress()->getCountryId();
            $products = $this->getProducts($shipperCountry, $recipientCountry);
            $productCodes = array_keys($products);
            if (count($productCodes) > 0) {
                $product = $productCodes[0];
            } else {
                Mage::throwException('No DHL product available for this shipment.');
            }
        }

        $request->setData('gk_api_product', $product);

        // add customs information to request
        $request->setData('customs', $httpRequest->getPost('customs', []));

        $response = new Varien_Object();

        try {
            // Build order configuration for label settings
            $shipment = $request->getOrderShipment();
            $settingsBuilder = $this->_getSettingsBuilder();
            $orderConfig = $settingsBuilder->build($shipment->getStoreId());

            // Call REST Client with Magento request (client handles SDK conversion internally)
            // This follows the SOAP Gateway pattern: Gateway accepts Magento requests,
            // converts to protocol-specific format internally
            $client = Mage::getModel('dhl_versenden/webservice_client_shipment');
            $shipments = $client->createShipments([$request], $orderConfig);

            // Validate results
            if (empty($shipments)) {
                Mage::throwException('The shipment request had errors.');
            }

            // Extract shipment data from REST response
            $restShipment = $shipments[0];
            // Merge PDF labels (SDK returns base64, adapter expects raw binary)
            $pdfAdapter = new \Dhl\Versenden\ParcelDe\Pdf\Adapter\Zend();
            $labelPages = array_map('base64_decode', array_filter($restShipment->getLabels()));
            $mergedLabel = $pdfAdapter->merge($labelPages);

            // Build response structure (maintain Magento 1 API contract)
            $responseData = [
                'info' => [[
                    'tracking_number' => $restShipment->getShipmentNumber(),
                    'label_content'   => $mergedLabel,
                ]],
            ];
            $response->setData($responseData);

        } catch (\Dhl\Sdk\ParcelDe\Shipping\Exception\DetailedServiceException $e) {
            // convert to Mage_Core_Exception for proper message display in frontend
            Mage::throwException($e->getMessage());
        } catch (\Dhl\Sdk\ParcelDe\Shipping\Exception\ServiceException $e) {
            // convert to Mage_Core_Exception for proper message display in frontend
            Mage::throwException('Web service request failed.');
        } catch (\InvalidArgumentException $e) {
            // Builder validation errors (from CustomsBuilder when international shipment data is incomplete)
            // convert to Mage_Core_Exception for proper message display in frontend
            Mage::throwException($e->getMessage());
        }

        return $response;
    }

    /**
     * Get the settings builder.
     *
     * @return Dhl_Versenden_Model_Webservice_Builder_Settings
     */
    protected function _getSettingsBuilder()
    {
        $factory = Mage::getModel('dhl_versenden/webservice_builder_factory');
        return $factory->createSettingsBuilder();
    }

    /**
     * @param string $type
     * @param string $code
     * @return bool|mixed
     */
    public function getCode($type, $code = '')
    {
        $codes = [
            'unit_of_measure' => [
                'G'   =>  Mage::helper('dhl_versenden')->__('Grams'),
                'KG'  =>  Mage::helper('dhl_versenden')->__('Kilograms'),
            ],
            'terms_of_trade' => [
                self::TOT_DDP => self::TOT_DDP,
                self::TOT_DXV => self::TOT_DXV,
                self::TOT_DDU => self::TOT_DDU,
                self::TOT_DDX => self::TOT_DDX,
            ],
        ];

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
     * @return false|Mage_Shipping_Model_Tracking_Result_Status
     */
    public function getTrackingInfo($tracking)
    {
        $trackData = [
            'carrier' => $this->_code,
            'carrier_title' => $this->getConfigData('title'),
            'progressdetail' => [],
            'tracking' => $tracking,
            'url' => 'http://nolp.dhl.de/nextt-online-public/set_identcodes.do?lang=de&idc=' . $tracking,
        ];

        return Mage::getModel('shipping/tracking_result_status', $trackData);
    }
}
