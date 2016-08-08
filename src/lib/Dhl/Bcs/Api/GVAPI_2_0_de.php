<?php

namespace Dhl\Bcs\Api;

class GVAPI_2_0_de extends \SoapClient
{

    /**
     * @var array $classmap The defined classes
     */
    private static $classmap = array (
      'Version' => 'Dhl\\Bcs\\Api\\Version',
      'AuthentificationType' => 'Dhl\\Bcs\\Api\\AuthentificationType',
      'NativeAddressType' => 'Dhl\\Bcs\\Api\\NativeAddressType',
      'ReceiverNativeAddressType' => 'Dhl\\Bcs\\Api\\ReceiverNativeAddressType',
      'PickupAddressType' => 'Dhl\\Bcs\\Api\\PickupAddressType',
      'DeliveryAddressType' => 'Dhl\\Bcs\\Api\\DeliveryAddressType',
      'BankType' => 'Dhl\\Bcs\\Api\\BankType',
      'NameType' => 'Dhl\\Bcs\\Api\\NameType',
      'ReceiverNameType' => 'Dhl\\Bcs\\Api\\ReceiverNameType',
      'CommunicationType' => 'Dhl\\Bcs\\Api\\CommunicationType',
      'ContactType' => 'Dhl\\Bcs\\Api\\ContactType',
      'PackStationType' => 'Dhl\\Bcs\\Api\\PackStationType',
      'PostfilialeType' => 'Dhl\\Bcs\\Api\\PostfilialeType',
      'ParcelShopType' => 'Dhl\\Bcs\\Api\\ParcelShopType',
      'CustomerType' => 'Dhl\\Bcs\\Api\\CustomerType',
      'ErrorType' => 'Dhl\\Bcs\\Api\\ErrorType',
      'CountryType' => 'Dhl\\Bcs\\Api\\CountryType',
      'ShipmentNumberType' => 'Dhl\\Bcs\\Api\\ShipmentNumberType',
      'Status' => 'Dhl\\Bcs\\Api\\Status',
      'Dimension' => 'Dhl\\Bcs\\Api\\Dimension',
      'TimeFrame' => 'Dhl\\Bcs\\Api\\TimeFrame',
      'GetVersionResponse' => 'Dhl\\Bcs\\Api\\GetVersionResponse',
      'CreateShipmentOrderRequest' => 'Dhl\\Bcs\\Api\\CreateShipmentOrderRequest',
      'ValidateShipmentOrderRequest' => 'Dhl\\Bcs\\Api\\ValidateShipmentOrderRequest',
      'CreateShipmentOrderResponse' => 'Dhl\\Bcs\\Api\\CreateShipmentOrderResponse',
      'ValidateShipmentResponse' => 'Dhl\\Bcs\\Api\\ValidateShipmentResponse',
      'GetLabelRequest' => 'Dhl\\Bcs\\Api\\GetLabelRequest',
      'GetLabelResponse' => 'Dhl\\Bcs\\Api\\GetLabelResponse',
      'DoManifestRequest' => 'Dhl\\Bcs\\Api\\DoManifestRequest',
      'DoManifestResponse' => 'Dhl\\Bcs\\Api\\DoManifestResponse',
      'DeleteShipmentOrderRequest' => 'Dhl\\Bcs\\Api\\DeleteShipmentOrderRequest',
      'DeleteShipmentOrderResponse' => 'Dhl\\Bcs\\Api\\DeleteShipmentOrderResponse',
      'GetExportDocRequest' => 'Dhl\\Bcs\\Api\\GetExportDocRequest',
      'GetExportDocResponse' => 'Dhl\\Bcs\\Api\\GetExportDocResponse',
      'GetManifestRequest' => 'Dhl\\Bcs\\Api\\GetManifestRequest',
      'GetManifestResponse' => 'Dhl\\Bcs\\Api\\GetManifestResponse',
      'UpdateShipmentOrderRequest' => 'Dhl\\Bcs\\Api\\UpdateShipmentOrderRequest',
      'UpdateShipmentOrderResponse' => 'Dhl\\Bcs\\Api\\UpdateShipmentOrderResponse',
      'CreationState' => 'Dhl\\Bcs\\Api\\CreationState',
      'ValidationState' => 'Dhl\\Bcs\\Api\\ValidationState',
      'Statusinformation' => 'Dhl\\Bcs\\Api\\Statusinformation',
      'PieceInformation' => 'Dhl\\Bcs\\Api\\PieceInformation',
      'ShipmentOrderType' => 'Dhl\\Bcs\\Api\\ShipmentOrderType',
      'Shipment' => 'Dhl\\Bcs\\Api\\Shipment',
      'ValidateShipmentOrderType' => 'Dhl\\Bcs\\Api\\ValidateShipmentOrderType',
      'ShipperTypeType' => 'Dhl\\Bcs\\Api\\ShipperTypeType',
      'ShipperType' => 'Dhl\\Bcs\\Api\\ShipperType',
      'ReceiverTypeType' => 'Dhl\\Bcs\\Api\\ReceiverTypeType',
      'ReceiverType' => 'Dhl\\Bcs\\Api\\ReceiverType',
      'Ident' => 'Dhl\\Bcs\\Api\\Ident',
      'ShipmentDetailsType' => 'Dhl\\Bcs\\Api\\ShipmentDetailsType',
      'ShipmentDetailsTypeType' => 'Dhl\\Bcs\\Api\\ShipmentDetailsTypeType',
      'ShipmentItemType' => 'Dhl\\Bcs\\Api\\ShipmentItemType',
      'ShipmentItemTypeType' => 'Dhl\\Bcs\\Api\\ShipmentItemTypeType',
      'ShipmentService' => 'Dhl\\Bcs\\Api\\ShipmentService',
      'Serviceconfiguration' => 'Dhl\\Bcs\\Api\\Serviceconfiguration',
      'ServiceconfigurationDetails' => 'Dhl\\Bcs\\Api\\ServiceconfigurationDetails',
      'ServiceconfigurationType' => 'Dhl\\Bcs\\Api\\ServiceconfigurationType',
      'ServiceconfigurationEndorsement' => 'Dhl\\Bcs\\Api\\ServiceconfigurationEndorsement',
      'ServiceconfigurationISR' => 'Dhl\\Bcs\\Api\\ServiceconfigurationISR',
      'ServiceconfigurationDH' => 'Dhl\\Bcs\\Api\\ServiceconfigurationDH',
      'ServiceconfigurationVisualAgeCheck' => 'Dhl\\Bcs\\Api\\ServiceconfigurationVisualAgeCheck',
      'ServiceconfigurationDeliveryTimeframe' => 'Dhl\\Bcs\\Api\\ServiceconfigurationDeliveryTimeframe',
      'ServiceconfigurationDateOfDelivery' => 'Dhl\\Bcs\\Api\\ServiceconfigurationDateOfDelivery',
      'ServiceconfigurationAdditionalInsurance' => 'Dhl\\Bcs\\Api\\ServiceconfigurationAdditionalInsurance',
      'ServiceconfigurationCashOnDelivery' => 'Dhl\\Bcs\\Api\\ServiceconfigurationCashOnDelivery',
      'ServiceconfigurationShipmentHandling' => 'Dhl\\Bcs\\Api\\ServiceconfigurationShipmentHandling',
      'ServiceconfigurationUnfree' => 'Dhl\\Bcs\\Api\\ServiceconfigurationUnfree',
      'ServiceconfigurationIC' => 'Dhl\\Bcs\\Api\\ServiceconfigurationIC',
      'ShipmentNotificationType' => 'Dhl\\Bcs\\Api\\ShipmentNotificationType',
      'ExportDocumentType' => 'Dhl\\Bcs\\Api\\ExportDocumentType',
      'ExportDocPosition' => 'Dhl\\Bcs\\Api\\ExportDocPosition',
      'FurtherAddressesType' => 'Dhl\\Bcs\\Api\\FurtherAddressesType',
      'DeliveryAdress' => 'Dhl\\Bcs\\Api\\DeliveryAdress',
      'LabelData' => 'Dhl\\Bcs\\Api\\LabelData',
      'ExportDocData' => 'Dhl\\Bcs\\Api\\ExportDocData',
      'ManifestState' => 'Dhl\\Bcs\\Api\\ManifestState',
      'DeletionState' => 'Dhl\\Bcs\\Api\\DeletionState',
      'BookPickupRequest' => 'Dhl\\Bcs\\Api\\BookPickupRequest',
      'BookPickupResponse' => 'Dhl\\Bcs\\Api\\BookPickupResponse',
      'PickupDetailsType' => 'Dhl\\Bcs\\Api\\PickupDetailsType',
      'PickupOrdererType' => 'Dhl\\Bcs\\Api\\PickupOrdererType',
      'PickupBookingInformationType' => 'Dhl\\Bcs\\Api\\PickupBookingInformationType',
      'CancelPickupRequest' => 'Dhl\\Bcs\\Api\\CancelPickupRequest',
      'CancelPickupResponse' => 'Dhl\\Bcs\\Api\\CancelPickupResponse',
      'IdentityData' => 'Dhl\\Bcs\\Api\\IdentityData',
      'DrivingLicense' => 'Dhl\\Bcs\\Api\\DrivingLicense',
      'IdentityCard' => 'Dhl\\Bcs\\Api\\IdentityCard',
      'BankCard' => 'Dhl\\Bcs\\Api\\BankCard',
      'PackstationType' => 'Dhl\\Bcs\\Api\\PackstationType',
      'ReadShipmentOrderResponse' => 'Dhl\\Bcs\\Api\\ReadShipmentOrderResponse',
    );

    /**
     * @param array $options A array of config values
     * @param string $wsdl The wsdl file to use
     */
    public function __construct(array $options = array(), $wsdl = null)
    {
      foreach (self::$classmap as $key => $value) {
        if (!isset($options['classmap'][$key])) {
          $options['classmap'][$key] = $value;
        }
      }
      $options = array_merge(array (
      'features' => 1,
    ), $options);
      if (!$wsdl) {
        $wsdl = 'https://cig.dhl.de/cig-wsdls/com/dpdhl/wsdl/geschaeftskundenversand-api/2.1/geschaeftskundenversand-api-2.1.wsdl';
      }
      parent::__construct($wsdl, $options);
    }

    /**
     * Creates shipments.
     *
     * @param CreateShipmentOrderRequest $part1
     * @return CreateShipmentOrderResponse
     */
    public function createShipmentOrder(CreateShipmentOrderRequest $part1)
    {
      return $this->__soapCall('createShipmentOrder', array($part1));
    }

    /**
     * Creates shipments.
     *
     * @param ValidateShipmentOrderRequest $part1
     * @return ValidateShipmentResponse
     */
    public function validateShipment(ValidateShipmentOrderRequest $part1)
    {
      return $this->__soapCall('validateShipment', array($part1));
    }

    /**
     * Deletes the requested shipments.
     *
     * @param DeleteShipmentOrderRequest $part1
     * @return DeleteShipmentOrderResponse
     */
    public function deleteShipmentOrder(DeleteShipmentOrderRequest $part1)
    {
      return $this->__soapCall('deleteShipmentOrder', array($part1));
    }

    /**
     * Manifest the requested DD shipments.
     *
     * @param DoManifestRequest $part1
     * @return DoManifestResponse
     */
    public function doManifest(DoManifestRequest $part1)
    {
      return $this->__soapCall('doManifest', array($part1));
    }

    /**
     * Returns the request-url for getting a label.
     *
     * @param GetLabelRequest $part1
     * @return GetLabelResponse
     */
    public function getLabel(GetLabelRequest $part1)
    {
      return $this->__soapCall('getLabel', array($part1));
    }

    /**
     * Returns the actual version of the implementation of the whole ISService
     *         webservice.
     *
     * @param Version $part1
     * @return GetVersionResponse
     */
    public function getVersion(Version $part1)
    {
      return $this->__soapCall('getVersion', array($part1));
    }

    /**
     * Returns the request-url for getting a export
     *         document.
     *
     * @param GetExportDocRequest $part1
     * @return GetExportDocResponse
     */
    public function getExportDoc(GetExportDocRequest $part1)
    {
      return $this->__soapCall('getExportDoc', array($part1));
    }

    /**
     * Request the manifest.
     *
     * @param GetManifestRequest $part1
     * @return GetManifestResponse
     */
    public function getManifest(GetManifestRequest $part1)
    {
      return $this->__soapCall('getManifest', array($part1));
    }

    /**
     * Updates a shipment order.
     *
     * @param UpdateShipmentOrderRequest $part1
     * @return UpdateShipmentOrderResponse
     */
    public function updateShipmentOrder(UpdateShipmentOrderRequest $part1)
    {
      return $this->__soapCall('updateShipmentOrder', array($part1));
    }

}
