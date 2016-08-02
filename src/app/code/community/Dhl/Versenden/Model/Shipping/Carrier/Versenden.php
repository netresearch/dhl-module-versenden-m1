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
use \Dhl\Versenden\Webservice\Adapter\Soap as SoapAdapter;
use \Dhl\Versenden\Webservice\Parser\Soap as SoapParser;
use \Dhl\Versenden\Webservice\RequestData;
use \Dhl\Versenden\Webservice\ResponseData;
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

    /**
     * Init carrier code
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_code = self::CODE;
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
     * Do request to shipment
     *
     * @param Mage_Shipping_Model_Shipment_Request $request
     * @return Varien_Object
     * @throws Exception
     */
    public function requestToShipment(Mage_Shipping_Model_Shipment_Request $request)
    {
        $wsHelper = Mage::helper('dhl_versenden/webservice');
        $wsType = Dhl_Versenden_Helper_Webservice::ADAPTER_TYPE_SOAP;
        $wsOperation = Dhl_Versenden_Helper_Webservice::OPERATION_CREATE_SHIPMENT_ORDER;

        $orderShipment = $request->getOrderShipment();
        $sequenceNumber = 1;
        $orderData = $request->getPackages();
        $orderData['product'] = RequestData\ShipmentOrder::PRODUCT_CODE_PAKET_NATIONAL;
        $shipmentOrder = $wsHelper->shipmentToShipmentOrder(
            $orderShipment,
            $sequenceNumber,
            $orderData
        );
        $shipmentOrders = new RequestData\ShipmentOrderCollection();
        $shipmentOrders->addItem($shipmentOrder);

        $wsVersion = new RequestData\Version('2', '1');
        $requestData = new RequestData\CreateShipment(
            $wsVersion,
            $shipmentOrders
        );

        /** @var SoapParser\CreateShipmentOrder $parser */
        $parser = $wsHelper->getWebserviceParser($wsType, $wsOperation);
        /** @var SoapAdapter $adapter */
        $adapter = $wsHelper->getWebserviceAdapter($wsType);

        $response = new Varien_Object();
        try {
            /** @var ResponseData\CreateShipment $result */
            $result = $adapter->createShipmentOrder($requestData, $parser);
            $resultSequence = $result->getSequence();
            $shipmentNumber = $resultSequence[$sequenceNumber];

            $responseData = array(
                'info' => array(array(
                    'tracking_number' => $shipmentNumber,
                    'label_content'   => $result->getLabels()->getItem($shipmentNumber)->getLabel(),
                ))
            );
            $response->setData($responseData);
        } catch (Exception $e) {
            Mage::logException($e);
            throw $e;
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
