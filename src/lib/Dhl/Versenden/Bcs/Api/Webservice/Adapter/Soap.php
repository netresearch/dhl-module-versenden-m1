<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Webservice\Adapter;

use \Dhl\Versenden\Bcs\Soap as VersendenApi;
use \Dhl\Versenden\Bcs\Api\Webservice\Adapter;
use \Dhl\Versenden\Bcs\Api\Webservice\Parser;
use \Dhl\Versenden\Bcs\Api\Webservice\RequestData;
use \Dhl\Versenden\Bcs\Api\Webservice\ResponseData;
use \Dhl\Versenden\Bcs\Api\Webservice\Parser\Soap as SoapParser;

class Soap implements Adapter
{
    /**
     * @var VersendenApi\GVAPI_2_0_de
     */
    private $soapClient;

    public function __construct(\SoapClient $soapClient)
    {
        $this->soapClient = $soapClient;
    }

    /**
     * @param RequestData\Version $requestData
     * @param SoapParser\Version $parser
     * @return ResponseData\Version
     */
    public function getVersion(RequestData $requestData, Parser $parser)
    {
        $requestType = Adapter\Soap\VersionRequestType::prepare($requestData);

        $response = $this->soapClient->getVersion($requestType);
        return $parser->parse($response);
    }

    /**
     * @param RequestData\CreateShipment $requestData
     * @param SoapParser\CreateShipmentOrder $parser
     * @return ResponseData\CreateShipment
     */
    public function createShipmentOrder(RequestData $requestData, Parser $parser)
    {
        $requestType = Adapter\Soap\CreateShipmentRequestType::prepare($requestData);

        $response = $this->soapClient->createShipmentOrder($requestType);
        $createShipment = $parser->parse($response);

        return $createShipment;
    }

    /**
     * @param RequestData\DeleteShipment $requestData
     * @param SoapParser\DeleteShipmentOrder $parser
     * @return ResponseData\DeleteShipment
     */
    public function deleteShipmentOrder(RequestData $requestData, Parser $parser)
    {
        $requestType = Adapter\Soap\DeleteShipmentRequestType::prepare($requestData);

        $response = $this->soapClient->deleteShipmentOrder($requestType);
        $deleteShipment = $parser->parse($response);

        return $deleteShipment;
    }

    /**
     * @param RequestData $requestData
     * @param Parser $parser
     * @throws NotImplementedException
     */
    public function getLabel(RequestData $requestData, Parser $parser)
    {
        throw new Adapter\NotImplementedException();
    }

    /**
     * @param RequestData $requestData
     * @param Parser $parser
     * @throws NotImplementedException
     */
    public function getExportDoc(RequestData $requestData, Parser $parser)
    {
        throw new Adapter\NotImplementedException();
    }

    /**
     * @param RequestData $requestData
     * @param Parser $parser
     * @throws NotImplementedException
     */
    public function doManifest(RequestData $requestData, Parser $parser)
    {
        throw new Adapter\NotImplementedException();
    }

    /**
     * @param RequestData $requestData
     * @param Parser $parser
     * @throws NotImplementedException
     */
    public function getManifest(RequestData $requestData, Parser $parser)
    {
        throw new Adapter\NotImplementedException();
    }

    /**
     * @param RequestData $requestData
     * @param Parser $parser
     * @throws NotImplementedException
     */
    public function updateShipmentOrder(RequestData $requestData, Parser $parser)
    {
        throw new Adapter\NotImplementedException();
    }

    /**
     * @param RequestData $requestData
     * @param Parser $parser
     * @throws NotImplementedException
     */
    public function validateShipment(RequestData $requestData, Parser $parser)
    {
        throw new Adapter\NotImplementedException();
    }

    /**
     * @return \SoapClient
     */
    public function getClient()
    {
        return $this->soapClient;
    }
}
