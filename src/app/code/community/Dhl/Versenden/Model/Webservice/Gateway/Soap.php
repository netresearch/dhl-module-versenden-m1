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
use \Dhl\Versenden\Bcs\Api\Webservice\Adapter\Soap as SoapAdapter;
use \Dhl\Versenden\Bcs\Api\Webservice\Parser\Soap as SoapParser;
use \Dhl\Versenden\Bcs\Api\Webservice\RequestData;
use \Dhl\Versenden\Bcs\Api\Webservice\ResponseData;
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
     * @return Webservice\Adapter\Soap
     */
    public function getAdapter(Dhl_Versenden_Model_Config_Shipper $config)
    {
        $options = array(
            'location' => $config->getEndpoint(),
            'login' => $config->getWebserviceAuthUsername(),
            'password' => $config->getWebserviceAuthPassword(),
            'trace' => 1
        );
        $client = new \Dhl\Versenden\Bcs\Soap\GVAPI_2_0_de($options);

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
            case self::OPERATION_DELETE_SHIPMENT_ORDER:
                return new Webservice\Parser\Soap\DeleteShipmentOrder();
            default:
                return null;
        }
    }

    /**
     * @param Dhl_Versenden_Model_Config $config
     * @param Dhl_Versenden_Model_Logger_Writer $writer
     * @return Dhl_Versenden_Model_Webservice_Logger_Soap
     */
    public function getLogger(Dhl_Versenden_Model_Config $config, Dhl_Versenden_Model_Logger_Writer $writer)
    {
        // hellcome to the world of composition over inheritance.
        $psrLogger = new Dhl_Versenden_Model_Logger_Mage($writer);

        $dhlLogger = Mage::getSingleton('dhl_versenden/log', array('config' => $config));
        $dhlLogger->setLogger($psrLogger);

        $soapLogger = new Dhl_Versenden_Model_Webservice_Logger_Soap($dhlLogger);
        return $soapLogger;
    }

    /**
     * @param Dhl_Versenden_Model_Webservice_Logger_Soap $logger
     * @param SoapAdapter $adapter
     * @param ResponseData\Status\Response $responseStatus
     */
    protected function logResult(
        Dhl_Versenden_Model_Webservice_Logger_Soap $logger,
        Webservice\Adapter\Soap $adapter,
        ResponseData\Status\Response $responseStatus
    ) {
        if ($responseStatus->isError()) {
            $logger->warning($adapter);
        } else {
            $logger->debug($adapter);
        }
    }

    /**
     * @param RequestData\CreateShipment $requestData
     * @return ResponseData\CreateShipment
     * @throws SoapFault
     */
    protected function doCreateShipmentOrder(RequestData\CreateShipment $requestData)
    {
        /** @var SoapParser\CreateShipmentOrder $parser */
        $parser = $this->getParser(self::OPERATION_CREATE_SHIPMENT_ORDER);
        /** @var SoapAdapter $adapter */
        $adapter = $this->getAdapter(Mage::getModel('dhl_versenden/config_shipper'));
        /** @var Dhl_Versenden_Model_Webservice_Logger_Soap $logger */
        $logger = $this->getLogger(
            Mage::getModel('dhl_versenden/config'),
            Mage::getModel('dhl_versenden/logger_writer')
        );

        try {
            /** @var ResponseData\CreateShipment $result */
            $result = $adapter->createShipmentOrder($requestData, $parser);
            $this->logResult($logger, $adapter, $result->getStatus());
        } catch (SoapFault $fault) {
            $logger->error($adapter);
            throw $fault;
        }

        return $result;
    }

    /**
     * @param RequestData\DeleteShipment $requestData
     * @return ResponseData\CreateShipment
     * @throws SoapFault
     */
    protected function doDeleteShipmentOrder(RequestData\DeleteShipment $requestData)
    {
        /** @var SoapParser\CreateShipmentOrder $parser */
        $parser = $this->getParser(self::OPERATION_DELETE_SHIPMENT_ORDER);
        /** @var SoapAdapter $adapter */
        $adapter = $this->getAdapter(Mage::getModel('dhl_versenden/config_shipper'));
        /** @var Dhl_Versenden_Model_Webservice_Logger_Soap $logger */
        $logger = $this->getLogger(
            Mage::getModel('dhl_versenden/config'),
            Mage::getModel('dhl_versenden/logger_writer')
        );

        try {
            /** @var ResponseData\CreateShipment $result */
            $result = $adapter->deleteShipmentOrder($requestData, $parser);
            $this->logResult($logger, $adapter, $result->getStatus());
        } catch (SoapFault $fault) {
            $logger->error($adapter);
            throw $fault;
        }

        return $result;
    }
}
