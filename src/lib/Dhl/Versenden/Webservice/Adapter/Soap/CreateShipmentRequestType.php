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
namespace Dhl\Versenden\Webservice\Adapter\Soap;
use Dhl\Bcs\Api as VersendenApi;
use Dhl\Versenden\Webservice\RequestData;

/**
 * CreateShipmentRequestType
 *
 * @category Dhl
 * @package  Dhl\Versenden\Webservice
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class CreateShipmentRequestType implements RequestType
{
    /**
     * @param RequestData\ShipmentOrder $shipmentOrder
     * @return VersendenApi\ShipmentDetailsTypeType
     */
    protected static function prepareShipmentDetails(RequestData\ShipmentOrder $shipmentOrder)
    {
        //TODO(nr): fill arguments
        return new VersendenApi\ShipmentDetailsTypeType('bla', 'bla', 'bla', 'bla');
    }

    /**
     * @param RequestData\Shipper $shipper
     * @return VersendenApi\ShipperType
     */
    protected static function prepareShipper(RequestData\Shipper $shipper)
    {
        $nameType = NameType::prepare($shipper);
        //TODO(nr): fill arguments
        return new VersendenApi\ShipperType($nameType, 'bla', 'bla');
    }

    /**
     * @param RequestData\Receiver $receiver
     * @return VersendenApi\ReceiverType
     */
    protected static function prepareReceiver(RequestData\Receiver $receiver)
    {
        $nameType = NameType::prepare($receiver);
        //TODO(nr): fill arguments
        return new VersendenApi\ReceiverType($nameType, 'bla', 'bla', 'bla', 'bla', 'bla');
    }

    /**
     * @param RequestData\ReturnReceiver $return
     * @return VersendenApi\ShipperType
     */
    protected static function prepareReturnReceiver(RequestData\ReturnReceiver $returnReceiver)
    {
        //TODO(nr): check if return service was chosen
        $nameType = NameType::prepare($returnReceiver);
        //TODO(nr): fill arguments
        return new VersendenApi\ShipperType($nameType, 'bla', 'bla');
    }

    /**
     * @param RequestData\ExportDocument $document
     * @return VersendenApi\ExportDocumentType
     */
    protected static function prepareExportDocument(RequestData\ExportDocument $document)
    {
        return new VersendenApi\ExportDocumentType('bla', 'bla', 'bla');
    }

    /**
     * @param RequestData\ShipmentOrder $shipmentOrder
     * @return VersendenApi\Shipment
     */
    protected static function prepareShipment(RequestData\ShipmentOrder $shipmentOrder)
    {
        $details = static::prepareShipmentDetails($shipmentOrder);
        //TODO(nr): rework data objects from config
        $shipper = static::prepareShipper($shipmentOrder->getShipper());
        $receiver = static::prepareReceiver($shipmentOrder->getReceiver());
        //TODO(nr): load optional data info shipment order
        $returnReceiver = static::prepareReturnReceiver($shipmentOrder->getReturnReceiver());
        $exportDocument = static::prepareExportDocument($shipmentOrder->getExportDocument());
        //TODO(nr): fill arguments
        return new VersendenApi\Shipment($details, $shipper, $receiver, $returnReceiver, $exportDocument);
    }

    /**
     * @param RequestData\ShipmentOrder $shipmentOrder
     * @return VersendenApi\ShipmentOrderType
     */
    protected static function prepareShipmentOrder(RequestData\ShipmentOrder $shipmentOrder)
    {
        $sequenceNumber = $shipmentOrder->getSequenceNumber();
        $shipment = static::prepareShipment($shipmentOrder);
        $printOnlyIfCodable = new VersendenApi\Serviceconfiguration($shipmentOrder->isPrintOnlyIfCodable());
        $labelResponseType = $shipmentOrder->getLabelResponseType();

        $requestType = new VersendenApi\ShipmentOrderType($sequenceNumber, $shipment);
        $requestType->setPrintOnlyIfCodeable($printOnlyIfCodable);
        $requestType->setLabelResponseType($labelResponseType);

        return $requestType;
    }

    /**
     * @param RequestData\CreateShipment $requestData
     * @return VersendenApi\CreateShipmentOrderRequest
     */
    public static function prepare(RequestData $requestData)
    {
        $version = new VersendenApi\Version(
            $requestData->getVersion()->getMajorRelease(),
            $requestData->getVersion()->getMinorRelease(),
            $requestData->getVersion()->getBuild()
        );


        $shipmentOrders = [];
        /** @var RequestData\ShipmentOrder $order */
        foreach ($requestData->getShipmentOrders() as $order) {
            $shipmentOrders[]= static::prepareShipmentOrder($order);
        }

        $requestType = new VersendenApi\CreateShipmentOrderRequest($version, $shipmentOrders);
        return $requestType;
    }
}
