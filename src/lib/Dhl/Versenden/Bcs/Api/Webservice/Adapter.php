<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Webservice;

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
