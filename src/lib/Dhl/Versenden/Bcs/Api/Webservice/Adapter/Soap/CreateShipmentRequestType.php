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
namespace Dhl\Versenden\Bcs\Api\Webservice\Adapter\Soap;
use Dhl\Versenden\Bcs\Soap as VersendenApi;
use Dhl\Versenden\Bcs\Api\Webservice\RequestData;

/**
 * CreateShipmentRequestType
 *
 * @category Dhl
 * @package  Dhl\Versenden\Bcs\Api\Webservice\Soap
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class CreateShipmentRequestType implements RequestType
{
    /**
     * Note: multiple packages are currently not supported by the DHL webservices,
     * so we pick the first one until this feature is added to the API.
     *
     * @param RequestData\ShipmentOrder $shipmentOrder
     * @return VersendenApi\ShipmentDetailsTypeType
     */
    protected static function prepareShipmentDetails(RequestData\ShipmentOrder $shipmentOrder)
    {
        $packages = $shipmentOrder->getPackages()->getItems();
        /** @var RequestData\ShipmentOrder\Package $package */
        $package = current($packages);
        $shipmentItemType = new VersendenApi\ShipmentItemType($package->getWeightInKG());

        $shipmentDetailsType = new VersendenApi\ShipmentDetailsTypeType(
            $shipmentOrder->getProductCode(),
            $shipmentOrder->getAccountNumber(),
            $shipmentOrder->getShipmentDate(),
            $shipmentItemType
        );
        $shipmentDetailsType->setCustomerReference($shipmentOrder->getReference());

        // add services that are part of the service type
        $serviceType = ServiceType::prepare($shipmentOrder->getServiceSelection());
        $shipmentDetailsType->setService($serviceType);

        // add services that are not part of the service type
        if ($shipmentOrder->getServiceSelection()->isReturnShipment()) {
            $shipmentDetailsType->setReturnShipmentAccountNumber($shipmentOrder->getReturnShipmentAccountNumber());
        }

        if ($shipmentOrder->getServiceSelection()->getParcelAnnouncement()) {
            $notificationType = new VersendenApi\ShipmentNotificationType($shipmentOrder->getReceiver()->getEmail());
            $shipmentDetailsType->setNotification($notificationType);
        }

        if ($shipmentOrder->getServiceSelection()->getCod()) {
            $bankDataType = BankType::prepare($shipmentOrder->getShipper()->getBankData());
            $shipmentDetailsType->setBankData($bankDataType);
        }

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
        $packStationType     = PostalFacilityType::prepare($receiver->getPackstation());
        $postfilialeType     = PostalFacilityType::prepare($receiver->getPostfiliale());
        $parcelShopType      = PostalFacilityType::prepare($receiver->getParcelShop());

        if ($packStationType || $postfilialeType || $parcelShopType) {
            $receiverAddressType = null;
        } else {
            $receiverAddressType = ReceiverAddressType::prepare($receiver);
        }
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
     * Note: multiple packages are currently not supported by the DHL webservices,
     * so we pick the first export document until this feature is added to the API.
     *
     * @param RequestData\ShipmentOrder\Export\DocumentCollection $documents
     * @return VersendenApi\ExportDocumentType|null
     */
    protected static function prepareExportDocument(RequestData\ShipmentOrder\Export\DocumentCollection $documents)
    {
        // the API apparently supports exactly one ExportDocumentType, no list
        $document = current($documents->getItems());
        if (!$document) {
            return null;
        }

        $exportDocType = new VersendenApi\ExportDocumentType(
            $document->getExportType(),
            $document->getPlaceOfCommital(),
            $document->getAdditionalFee()
        );
        $exportDocType->setInvoiceNumber($document->getInvoiceNumber());
        $exportDocType->setExportTypeDescription($document->getExportTypeDescription());
        $exportDocType->setTermsOfTrade($document->getTermsOfTrade());
        $exportDocType->setPermitNumber($document->getPermitNumber());
        $exportDocType->setAttestationNumber($document->getAttestationNumber());
        $exportDocType->setWithElectronicExportNtfctn(
            new VersendenApi\Serviceconfiguration($document->isElectronicExportNotification())
        );

        $exportDocPositions = [];
        /** @var RequestData\ShipmentOrder\Export\Position $position */
        foreach ($document->getPositions() as $position) {
            $exportDocPosition = new VersendenApi\ExportDocPosition(
                $position->getDescription(),
                $position->getCountryCodeOrigin(),
                $position->getTariffNumber(),
                $position->getAmount(),
                $position->getNetWeightInKG(),
                $position->getValue()
            );
            $exportDocPositions[]= $exportDocPosition;
        }
        $exportDocType->setExportDocPosition($exportDocPositions);

        return $exportDocType;
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

        // remove email adress from communication object if parcelanouncement is set to no
        if (!$shipmentOrder->getServiceSelection()->getParcelAnnouncement()) {
            $receiver->getCommunication()->setEmail(null);
        }

        if ($shipmentOrder->getServiceSelection()->isReturnShipment()) {
            $returnReceiver = static::prepareReturnReceiver($shipmentOrder->getShipper()->getReturnReceiver());
        } else {
            $returnReceiver = null;
        }

        $exportDocument = static::prepareExportDocument($shipmentOrder->getExportDocuments());

        $shipment = new VersendenApi\Shipment(
            $details,
            $shipper,
            $receiver,
            $returnReceiver,
            $exportDocument
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

        $requestType = new VersendenApi\ShipmentOrderType(
            $shipmentOrder->getSequenceNumber(),
            $shipment
        );

        $printOnlyIfCodeable = $shipmentOrder->getServiceSelection()->isPrintOnlyIfCodeable();
        $printOnlyIfCodeable = new VersendenApi\Serviceconfiguration($printOnlyIfCodeable);
        $requestType->setPrintOnlyIfCodeable($printOnlyIfCodeable);

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
