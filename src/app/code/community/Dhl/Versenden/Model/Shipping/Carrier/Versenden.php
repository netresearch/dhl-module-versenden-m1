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

    const PRODUCT_CODE_PAKET_NATIONAL  = 'V01PAK';
    const PRODUCT_CODE_WELTPAKET = 'V53WPAK';
    const PRODUCT_CODE_EUROPAKET = 'V54EPAK';
    const PRODUCT_CODE_KURIER_TAGGLEICH = 'V06TG';
    const PRODUCT_CODE_KURIER_WUNSCHZEIT = 'V06WZ';
    const PRODUCT_CODE_PAKET_AUSTRIA = 'V86PARCEL';
    const PRODUCT_CODE_PAKET_CONNECT = 'V87PARCEL';
    const PRODUCT_CODE_PAKET_INTERNATIONAL = 'V82PARCEL';

    /**
     * Init carrier code
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_code = self::CODE;
    }

    /**
     * @return string[]
     */
    protected function getProducts()
    {
        return array(
            self::PRODUCT_CODE_PAKET_NATIONAL => Mage::helper('dhl_versenden/data')->__('DHL Paket National'),
            self::PRODUCT_CODE_WELTPAKET => Mage::helper('dhl_versenden/data')->__('DHL Weltpaket'),
            self::PRODUCT_CODE_EUROPAKET => Mage::helper('dhl_versenden/data')->__('DHL Europaket'),
            self::PRODUCT_CODE_KURIER_TAGGLEICH => Mage::helper('dhl_versenden/data')->__('DHL Kurier Taggleich'),
            self::PRODUCT_CODE_KURIER_WUNSCHZEIT => Mage::helper('dhl_versenden/data')->__('DHL Kurier Wunschzeit'),
            self::PRODUCT_CODE_PAKET_AUSTRIA => Mage::helper('dhl_versenden/data')->__('DHL Paket Austria'),
            self::PRODUCT_CODE_PAKET_CONNECT => Mage::helper('dhl_versenden/data')->__('DHL PAKET Connect'),
            self::PRODUCT_CODE_PAKET_INTERNATIONAL => Mage::helper('dhl_versenden/data')->__('DHL PAKET International'),
        );
    }

    /**
     * Obtain DHL products for national shipping.
     *
     * @return string[]
     */
    protected function getNationalProducts()
    {
        $products = $this->getProducts();
        return array(
            self::PRODUCT_CODE_PAKET_NATIONAL => $products[self::PRODUCT_CODE_PAKET_NATIONAL],
        );
    }

    /**
     * Obtain DHL products for international shipping.
     *
     * @return string[]
     */
    protected function getInternationalProducts()
    {
        $products = $this->getProducts();
        return array(
            self::PRODUCT_CODE_WELTPAKET  => $products[self::PRODUCT_CODE_WELTPAKET],
        );
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
     * @return array
     */
    public function getContainerTypes(Varien_Object $params = null)
    {
        if (!$params) {
            return $this->getProducts();
        }

        $isNationalShipping = ($params->getData('country_shipper') == 'DE')
            && ($params->getData('country_recipient') == 'DE');

        if ($isNationalShipping) {
            return $this->getNationalProducts();
        } else {
            return $this->getInternationalProducts();
        }
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
        $sequenceNumber = 0;
        $shipmentRequests = array(
            $sequenceNumber => $request,
        );

        $response = new Varien_Object();

        try {
            $requestData = new Webservice\RequestData\Version('2', '1', null);
            $parser = new Webservice\Parser\Soap\Version();
            $adapter = Mage::getModel('dhl_versenden/webservice_gateway_soap')
                ->getAdapter(Mage::getModel('dhl_versenden/config_shipper'));
            $adapter->getVersion($requestData, $parser);

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
                'KG'   =>  Mage::helper('dhl_versenden')->__('Kilograms'),
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
