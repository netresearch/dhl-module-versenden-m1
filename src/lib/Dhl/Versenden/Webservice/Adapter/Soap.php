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
 * @package   Dhl\Versenden\Webservice
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
namespace Dhl\Versenden\Webservice\Adapter;
use Dhl\Versenden\Webservice\Adapter;

/**
 * Adapter
 *
 * @category Dhl
 * @package  Dhl\Versenden\Webservice\Adapter
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Soap implements Adapter
{
    /**
     * @param $requestData
     * @param $versionParser
     * @throws Adapter\NotImplementedException
     */
    public function getVersion($requestData, $versionParser)
    {
        throw new Adapter\NotImplementedException();
    }

    /**
     * @param $requestData
     * @param $shipmentOrderParser
     * @throws NotImplementedException
     */
    public function createShipmentOrder($requestData, $shipmentOrderParser)
    {
        throw new Adapter\NotImplementedException();
    }

    /**
     * @param $requestData
     * @param $statusParser
     * @throws NotImplementedException
     */
    public function deleteShipmentOrder($requestData, $statusParser)
    {
        throw new Adapter\NotImplementedException();
    }

    /**
     * @param $requestData
     * @param $shipmentOrderParser
     * @throws NotImplementedException
     */
    public function getLabel($requestData, $shipmentOrderParser)
    {
        throw new Adapter\NotImplementedException();
    }

    /**
     * @param $requestData
     * @param $exportDocParser
     * @throws NotImplementedException
     */
    public function getExportDoc($requestData, $exportDocParser)
    {
        throw new Adapter\NotImplementedException();
    }

    /**
     * @param $requestData
     * @param $manifestStateParser
     * @throws NotImplementedException
     */
    public function doManifest($requestData, $manifestStateParser)
    {
        throw new Adapter\NotImplementedException();
    }

    /**
     * @param $requestData
     * @param $manifestParser
     * @throws NotImplementedException
     */
    public function getManifest($requestData, $manifestParser)
    {
        throw new Adapter\NotImplementedException();
    }

    /**
     * @param $requestData
     * @param $shipmentOrderParser
     * @throws NotImplementedException
     */
    public function updateShipmentOrder($requestData, $shipmentOrderParser)
    {
        throw new Adapter\NotImplementedException();
    }

    /**
     * @param $requestData
     * @param $statusParser
     * @throws NotImplementedException
     */
    public function validateShipment($requestData, $statusParser)
    {
        throw new Adapter\NotImplementedException();
    }
}
