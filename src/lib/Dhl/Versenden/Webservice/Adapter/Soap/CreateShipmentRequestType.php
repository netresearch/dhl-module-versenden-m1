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
 * @package   Dhl\Versenden\Webservice\Soap
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
namespace Dhl\Versenden\Webservice\Adapter\Soap;
use Dhl\Bcs\Api as VersendenApi;
use Dhl\Bcs\Api\ShipmentItemType;
use Dhl\Versenden\Webservice\RequestData;

/**
 * CreateShipmentRequestType
 *
 * @category Dhl
 * @package  Dhl\Versenden\Webservice\Soap
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
        //TODO(nr): how to add multiple parcels/packages?
        $packages = $shipmentOrder->getPackages()->getItems();
        /** @var RequestData\ShipmentOrder\Package $package */
        $package = current($packages);
        $shipmentItemType = new ShipmentItemType($package->getWeightInKG());

        $shipmentDetailsType = new VersendenApi\ShipmentDetailsTypeType(
            $shipmentOrder->getProductCode(),
            $shipmentOrder->getAccountNumber(),
            $shipmentOrder->getShipmentDate(),
            $shipmentItemType
        );
        $shipmentDetailsType->setCustomerReference($shipmentOrder->getReference());

        return $shipmentDetailsType;
    }

    /**
     * @param RequestData\ShipmentOrder\Shipper\Contact $shipper
     * @return VersendenApi\ShipperType
     */
    protected static function prepareShipper(RequestData\ShipmentOrder\Shipper\Contact $shipper)
    {
        $nameType          = NameType::prepare($shipper);
        $addressType       = AddressType::prepare($shipper);
        $communicationType = CommunicationType::prepare($shipper);

        $shipperType = new VersendenApi\ShipperType(
            $nameType,
            $addressType,
            $communicationType
        );

        return $shipperType;
    }

    /**
     * @param RequestData\ShipmentOrder\Receiver $receiver
     * @return VersendenApi\ReceiverType
     */
    protected static function prepareReceiver(RequestData\ShipmentOrder\Receiver $receiver)
    {
        $receiverAddressType = ReceiverAddressType::prepare($receiver);
        $packStationType     = PostalFacilityType::prepare($receiver->getPackstation());
        $postfilialeType     = PostalFacilityType::prepare($receiver->getPostfiliale());
        $parcelShopType      = PostalFacilityType::prepare($receiver->getParcelShop());
        $communicationType   = CommunicationType::prepare($receiver);

        $receiverType = new VersendenApi\ReceiverType(
            $receiver->getName1(),
            $receiverAddressType,
            $packStationType,
            $postfilialeType,
            $parcelShopType,
            $communicationType
        );

        return $receiverType;
    }

    /**
     * @param RequestData\ShipmentOrder\Shipper\Contact $returnReceiver
     * @return VersendenApi\ShipperType
     */
    protected static function prepareReturnReceiver(RequestData\ShipmentOrder\Shipper\Contact $returnReceiver)
    {
        //TODO(nr): check if return service was chosen
        $nameType          = NameType::prepare($returnReceiver);
        $addressType       = AddressType::prepare($returnReceiver);
        $communicationType = CommunicationType::prepare($returnReceiver);

        $returnReceiverType = new VersendenApi\ShipperType(
            $nameType,
            $addressType,
            $communicationType
        );

        return $returnReceiverType;
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
        $details        = static::prepareShipmentDetails($shipmentOrder);
        $shipper        = static::prepareShipper($shipmentOrder->getShipper()->getContact());
        $receiver       = static::prepareReceiver($shipmentOrder->getReceiver());
        $returnReceiver = static::prepareReturnReceiver($shipmentOrder->getShipper()->getReturnReceiver());

        //TODO(nr): DHLGKP-22
//        $exportDocument = static::prepareExportDocument($shipmentOrder->getExportDocument());

        $shipment = new VersendenApi\Shipment(
            $details,
            $shipper,
            $receiver,
            $returnReceiver,
            null
        );

        return $shipment;
    }

    /**
     * @param RequestData\ShipmentOrder $shipmentOrder
     * @return VersendenApi\ShipmentOrderType
     */
    protected static function prepareShipmentOrder(RequestData\ShipmentOrder $shipmentOrder)
    {
        $shipment           = static::prepareShipment($shipmentOrder);
        $printOnlyIfCodable = new VersendenApi\Serviceconfiguration($shipmentOrder->isPrintOnlyIfCodable());

        $requestType = new VersendenApi\ShipmentOrderType(
            $shipmentOrder->getSequenceNumber(),
            $shipment
        );

        $requestType->setPrintOnlyIfCodeable($printOnlyIfCodable);
        $requestType->setLabelResponseType($shipmentOrder->getLabelResponseType());

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

        $requestType = new VersendenApi\CreateShipmentOrderRequest(
            $version,
            $shipmentOrders
        );

        return $requestType;
    }
}
