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
use \Dhl\Versenden\Webservice\Adapter\Soap as SoapAdapter;
use \Dhl\Versenden\Webservice\Parser\Soap as SoapParser;
use \Dhl\Versenden\Webservice\RequestData;
use \Dhl\Versenden\Webservice\ResponseData;
/**
 * Dhl_Versenden_Model_Webservice_Gateway_Soap
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Model_Webservice_Gateway_Soap
    extends Dhl_Versenden_Model_Webservice_Gateway_Abstract
    implements Dhl_Versenden_Model_Webservice_Gateway
{
    /**
     * @param Dhl_Versenden_Model_Config_Shipper $config shipper configuration object
     *
     * @param Dhl_Versenden_Model_Config_Shipper $config
     * @return Webservice\Adapter
     */
    public function getAdapter(Dhl_Versenden_Model_Config_Shipper $config)
    {
        $options = array(
            'location' => $config->getEndpoint(),
            'login' => $config->getWebserviceAuthUsername(),
            'password' => $config->getWebserviceAuthPassword(),
            'trace' => 1
        );
        $client = new \Dhl\Bcs\Api\GVAPI_2_0_de($options);

        $authHeader = new \SoapHeader(
            'http://dhl.de/webservice/cisbase',
            'Authentification',
            array(
                'user' => $config->getAccountSettings()->getUser(),
                'signature' => $config->getAccountSettings()->getSignature(),
            )
        );
        $client->__setSoapHeaders($authHeader);

        return new Webservice\Adapter\Soap($client);
    }

    /**
     * @param string $operation
     * @return Webservice\Parser
     */
    public function getParser($operation)
    {
        switch ($operation) {
            case self::OPERATION_GET_VERSION:
                return new Webservice\Parser\Soap\Version();
            case self::OPERATION_CREATE_SHIPMENT_ORDER:
                return new Webservice\Parser\Soap\CreateShipmentOrder();
            default:
                return null;
        }
    }

    /**
     * @param Mage_Shipping_Model_Shipment_Request[] $shipmentRequests
     * @return RequestData\ShipmentOrderCollection
     */
    protected function prepareShipmentOrders(array $shipmentRequests)
    {
        $shipmentOrderCollection = new RequestData\ShipmentOrderCollection();

        /** @var Mage_Shipping_Model_Shipment_Request $shipmentRequest */
        foreach ($shipmentRequests as $sequenceNumber => $shipmentRequest) {
            $orderShipment = $shipmentRequest->getOrderShipment();

            $packageInfo = $shipmentRequest->getData('packages');
            $serviceInfo = $shipmentRequest->getData('services');

            try {
                $shipmentOrder = $this->shipmentToShipmentOrder(
                    $sequenceNumber,
                    $orderShipment,
                    $packageInfo,
                    $serviceInfo
                );

                $canShipPartially = ($shipmentOrder->getServiceSelection()->getCod() === null)
                    && ($shipmentOrder->getServiceSelection()->getInsurance() === null);
                $isPartial = ($orderShipment->getOrder()->getTotalQtyOrdered() != $orderShipment->getTotalQty());
                if (!$canShipPartially && $isPartial) {
                    $message = 'Cannot do partial shipment with COD or Additional Insurance.';
                    $message = Mage::helper('dhl_versenden/data')->__($message);
                    throw new RequestData\ValidationException($message);
                }

                $shipmentOrderCollection->addItem($shipmentOrder);
            } catch (RequestData\ValidationException $e) {
                $shipmentRequest->setData('request_data_exception', $e->getMessage());
            }
        }

        return $shipmentOrderCollection;
    }

    /**
     * @param Mage_Shipping_Model_Shipment_Request[] $shipmentRequests
     * @return ResponseData\CreateShipment
     * @throws SoapFault
     * @throws RequestData\ValidationException
     */
    protected function _createShipmentOrder(array $shipmentRequests)
    {
        $wsVersion = new RequestData\Version('2', '1');
        $shipmentOrderCollection = $this->prepareShipmentOrders($shipmentRequests);

        // handle validation errors in shipment request data
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

        if (count($shipmentOrderErrors)) {
            $msg = sprintf('%s %s', 'The shipment request(s) had errors.', implode("\n", $shipmentOrderErrors));
            throw new RequestData\ValidationException($msg);
        }

        /** @var RequestData\CreateShipment $requestData */
        $requestData = new RequestData\CreateShipment($wsVersion, $shipmentOrderCollection);
        /** @var SoapParser\CreateShipmentOrder $parser */
        $parser = $this->getParser(self::OPERATION_CREATE_SHIPMENT_ORDER);
        /** @var SoapAdapter $adapter */
        $adapter = $this->getAdapter(Mage::getModel('dhl_versenden/config_shipper'));

        try {
            /** @var ResponseData\CreateShipment $result */
            $result = $adapter->createShipmentOrder($requestData, $parser);
        } catch (SoapFault $fault) {
            $this->logRequest($adapter);
            $this->logResponse($adapter);
            throw $fault;
        }

        return $result;
    }

    /**
     * @param SoapAdapter $adapter
     */
    public function logRequest(SoapAdapter $adapter)
    {
        $request = $adapter->getClient()->__getLastRequest();
        Mage::getSingleton('core/logger')->log($request);
    }

    /**
     * @param SoapAdapter $adapter
     */
    public function logResponse(SoapAdapter $adapter)
    {
        $headers = $adapter->getClient()->__getLastResponseHeaders();
        $response = $adapter->getClient()->__getLastResponse();
        Mage::getSingleton('core/logger')->log($headers . "\n\n" . $response);
    }
}
