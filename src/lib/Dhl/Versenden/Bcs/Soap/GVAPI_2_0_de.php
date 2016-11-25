<?php

namespace Dhl\Versenden\Bcs\Soap;

class GVAPI_2_0_de extends \SoapClient
{

    /**
     * @var array $classmap The defined classes
     */
    private static $classmap = array (
      'Version' => 'Dhl\\Versenden\\Bcs\\Soap\\Version',
      'GetVersionResponse' => 'Dhl\\Versenden\\Bcs\\Soap\\GetVersionResponse',
      'CreateShipmentOrderRequest' => 'Dhl\\Versenden\\Bcs\\Soap\\CreateShipmentOrderRequest',
      'CreateShipmentOrderResponse' => 'Dhl\\Versenden\\Bcs\\Soap\\CreateShipmentOrderResponse',
      'ShipmentOrderType' => 'Dhl\\Versenden\\Bcs\\Soap\\ShipmentOrderType',
      'Shipment' => 'Dhl\\Versenden\\Bcs\\Soap\\Shipment',
      'ShipmentDetailsTypeType' => 'Dhl\\Versenden\\Bcs\\Soap\\ShipmentDetailsTypeType',
      'ShipmentDetailsType' => 'Dhl\\Versenden\\Bcs\\Soap\\ShipmentDetailsType',
      'ShipmentItemType' => 'Dhl\\Versenden\\Bcs\\Soap\\ShipmentItemType',
      'ShipmentService' => 'Dhl\\Versenden\\Bcs\\Soap\\ShipmentService',
      'ServiceconfigurationDateOfDelivery' => 'Dhl\\Versenden\\Bcs\\Soap\\ServiceconfigurationDateOfDelivery',
      'ServiceconfigurationDeliveryTimeframe' => 'Dhl\\Versenden\\Bcs\\Soap\\ServiceconfigurationDeliveryTimeframe',
      'ServiceconfigurationISR' => 'Dhl\\Versenden\\Bcs\\Soap\\ServiceconfigurationISR',
      'Serviceconfiguration' => 'Dhl\\Versenden\\Bcs\\Soap\\Serviceconfiguration',
      'ServiceconfigurationShipmentHandling' => 'Dhl\\Versenden\\Bcs\\Soap\\ServiceconfigurationShipmentHandling',
      'ServiceconfigurationEndorsement' => 'Dhl\\Versenden\\Bcs\\Soap\\ServiceconfigurationEndorsement',
      'ServiceconfigurationVisualAgeCheck' => 'Dhl\\Versenden\\Bcs\\Soap\\ServiceconfigurationVisualAgeCheck',
      'ServiceconfigurationDetails' => 'Dhl\\Versenden\\Bcs\\Soap\\ServiceconfigurationDetails',
      'ServiceconfigurationCashOnDelivery' => 'Dhl\\Versenden\\Bcs\\Soap\\ServiceconfigurationCashOnDelivery',
      'ServiceconfigurationAdditionalInsurance' => 'Dhl\\Versenden\\Bcs\\Soap\\ServiceconfigurationAdditionalInsurance',
      'ServiceconfigurationIC' => 'Dhl\\Versenden\\Bcs\\Soap\\ServiceconfigurationIC',
      'Ident' => 'Dhl\\Versenden\\Bcs\\Soap\\Ident',
      'ShipmentNotificationType' => 'Dhl\\Versenden\\Bcs\\Soap\\ShipmentNotificationType',
      'BankType' => 'Dhl\\Versenden\\Bcs\\Soap\\BankType',
      'ShipperType' => 'Dhl\\Versenden\\Bcs\\Soap\\ShipperType',
      'ShipperTypeType' => 'Dhl\\Versenden\\Bcs\\Soap\\ShipperTypeType',
      'NameType' => 'Dhl\\Versenden\\Bcs\\Soap\\NameType',
      'NativeAddressType' => 'Dhl\\Versenden\\Bcs\\Soap\\NativeAddressType',
      'CountryType' => 'Dhl\\Versenden\\Bcs\\Soap\\CountryType',
      'CommunicationType' => 'Dhl\\Versenden\\Bcs\\Soap\\CommunicationType',
      'ReceiverType' => 'Dhl\\Versenden\\Bcs\\Soap\\ReceiverType',
      'ReceiverTypeType' => 'Dhl\\Versenden\\Bcs\\Soap\\ReceiverTypeType',
      'ReceiverNativeAddressType' => 'Dhl\\Versenden\\Bcs\\Soap\\ReceiverNativeAddressType',
      'cis:PackStationType' => 'Dhl\\Versenden\\Bcs\\Soap\\PackStationType',
      'cis:PostfilialeType' => 'Dhl\\Versenden\\Bcs\\Soap\\PostfilialeType',
      'cis:ParcelShopType' => 'Dhl\\Versenden\\Bcs\\Soap\\ParcelShopType',
      'ExportDocumentType' => 'Dhl\\Versenden\\Bcs\\Soap\\ExportDocumentType',
      'ExportDocPosition' => 'Dhl\\Versenden\\Bcs\\Soap\\ExportDocPosition',
      'Statusinformation' => 'Dhl\\Versenden\\Bcs\\Soap\\Statusinformation',
      'CreationState' => 'Dhl\\Versenden\\Bcs\\Soap\\CreationState',
      'LabelData' => 'Dhl\\Versenden\\Bcs\\Soap\\LabelData',
      'DeleteShipmentOrderRequest' => 'Dhl\\Versenden\\Bcs\\Soap\\DeleteShipmentOrderRequest',
      'DeleteShipmentOrderResponse' => 'Dhl\\Versenden\\Bcs\\Soap\\DeleteShipmentOrderResponse',
      'DeletionState' => 'Dhl\\Versenden\\Bcs\\Soap\\DeletionState',
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
        $wsdl = 'https://cig.dhl.de/cig-wsdls/com/dpdhl/wsdl/geschaeftskundenversand-api/2.2/geschaeftskundenversand-api-2.2.wsdl';
      }
      parent::__construct($wsdl, $options);
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
     * Deletes the requested shipments.
     *
     * @param DeleteShipmentOrderRequest $part1
     * @return DeleteShipmentOrderResponse
     */
    public function deleteShipmentOrder(DeleteShipmentOrderRequest $part1)
    {
      return $this->__soapCall('deleteShipmentOrder', array($part1));
    }

}
