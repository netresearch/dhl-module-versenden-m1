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
 * @package   Dhl\Versenden\Bcs\Api\Webservice
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
namespace Dhl\Versenden\Bcs\Api\Webservice;
/**
 * Adapter
 *
 * @category Dhl
 * @package  Dhl\Versenden\Bcs\Api\Webservice
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
interface Adapter
{
    public function getVersion(RequestData $requestData, Parser $versionParser);
    public function createShipmentOrder(RequestData $requestData, Parser $shipmentOrderParser);
    public function deleteShipmentOrder(RequestData $requestData, Parser $statusParser);
    public function getLabel(RequestData $requestData, Parser $shipmentOrderParser);
    public function getExportDoc(RequestData $requestData, Parser $exportDocParser);
    public function doManifest(RequestData $requestData, Parser $manifestStateParser);
    public function getManifest(RequestData $requestData, Parser $manifestParser);
    public function updateShipmentOrder(RequestData $requestData, Parser $shipmentOrderParser);
    public function validateShipment(RequestData $requestData, Parser $statusParser);
}
