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
     * @return Webservice\Adapter
     */
    public function getAdapter(Dhl_Versenden_Model_Config_Shipper $config)
    {
        $options = array(
            'location' => $config->getEndpoint(),
            'login' => $config->getWebserviceAuthUsername(),
            'password' => $config->getWebserviceAuthPassword(),
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
     * @return ResponseData\CreateShipment
     */
    public function createShipmentOrder(array $shipmentRequests)
    {
        //TODO(nr): dispatch request_before event

        $wsVersion = new RequestData\Version('2', '1');
        $shipmentOrders = new RequestData\ShipmentOrderCollection();

        foreach ($shipmentRequests as $sequenceNumber => $shipmentRequest) {
            $orderShipment = $shipmentRequest->getOrderShipment();

            $packages = $shipmentRequest->getPackages();
            //TODO(nr): calculate or fetch from POST data
            $package = $packages[1]['params'];
            $orderData['product'] = $package['container'];
            $orderData['shipment_service'] = array();
            $orderData['service_setting'] = array();

            $shipmentOrder = $this->shipmentToShipmentOrder(
                $orderShipment,
                $sequenceNumber,
                $orderData
            );

            $shipmentOrders->addItem($shipmentOrder);
        }


        /** @var RequestData\CreateShipment $requestData */
        $requestData = new RequestData\CreateShipment($wsVersion, $shipmentOrders);
        /** @var SoapParser\CreateShipmentOrder $parser */
        $parser = $this->getParser(self::OPERATION_CREATE_SHIPMENT_ORDER);
        /** @var SoapAdapter $adapter */
        $adapter = $this->getAdapter(Mage::getModel('dhl_versenden/config_shipper'));
        /** @var ResponseData\CreateShipment $result */
        $result = $adapter->createShipmentOrder($requestData, $parser);

        //TODO(nr): dispatch request_after event
        return $result;
    }
}
