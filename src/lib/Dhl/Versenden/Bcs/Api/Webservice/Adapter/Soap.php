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
 * @package   Dhl\Versenden\Bcs\Api\Webservice\Soap
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
namespace Dhl\Versenden\Bcs\Api\Webservice\Adapter;
use \Dhl\Versenden\Bcs\Soap as VersendenApi;
use \Dhl\Versenden\Bcs\Api\Webservice\Adapter;
use \Dhl\Versenden\Bcs\Api\Webservice\Parser;
use \Dhl\Versenden\Bcs\Api\Webservice\RequestData;
use \Dhl\Versenden\Bcs\Api\Webservice\ResponseData;
use \Dhl\Versenden\Bcs\Api\Webservice\Parser\Soap as SoapParser;

/**
 * Soap
 *
 * @category Dhl
 * @package  Dhl\Versenden\Bcs\Api\Webservice\Soap
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
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
