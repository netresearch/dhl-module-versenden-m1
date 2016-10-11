<?php

namespace Dhl\Bcs\Api;

class GVAPI_2_0_de extends \SoapClient
{

    /**
     * @var array $classmap The defined classes
     */
    private static $classmap = array (
      'CreateShipmentOrderRequest' => 'Dhl\\Bcs\\Api\\CreateShipmentOrderRequest',
      'CreateShipmentOrderResponse' => 'Dhl\\Bcs\\Api\\CreateShipmentOrderResponse',
      'Version' => 'Dhl\\Bcs\\Api\\Version',
      'ShipmentOrderType' => 'Dhl\\Bcs\\Api\\ShipmentOrderType',
      'Shipment' => 'Dhl\\Bcs\\Api\\Shipment',
      'ShipmentDetailsTypeType' => 'Dhl\\Bcs\\Api\\ShipmentDetailsTypeType',
      'ShipmentDetailsType' => 'Dhl\\Bcs\\Api\\ShipmentDetailsType',
      'ShipmentItemType' => 'Dhl\\Bcs\\Api\\ShipmentItemType',
      'ShipmentService' => 'Dhl\\Bcs\\Api\\ShipmentService',
      'ServiceconfigurationDateOfDelivery' => 'Dhl\\Bcs\\Api\\ServiceconfigurationDateOfDelivery',
      'ServiceconfigurationDeliveryTimeframe' => 'Dhl\\Bcs\\Api\\ServiceconfigurationDeliveryTimeframe',
      'ServiceconfigurationISR' => 'Dhl\\Bcs\\Api\\ServiceconfigurationISR',
      'Serviceconfiguration' => 'Dhl\\Bcs\\Api\\Serviceconfiguration',
      'ServiceconfigurationShipmentHandling' => 'Dhl\\Bcs\\Api\\ServiceconfigurationShipmentHandling',
      'ServiceconfigurationEndorsement' => 'Dhl\\Bcs\\Api\\ServiceconfigurationEndorsement',
      'ServiceconfigurationVisualAgeCheck' => 'Dhl\\Bcs\\Api\\ServiceconfigurationVisualAgeCheck',
      'ServiceconfigurationDetails' => 'Dhl\\Bcs\\Api\\ServiceconfigurationDetails',
      'ServiceconfigurationCashOnDelivery' => 'Dhl\\Bcs\\Api\\ServiceconfigurationCashOnDelivery',
      'ServiceconfigurationAdditionalInsurance' => 'Dhl\\Bcs\\Api\\ServiceconfigurationAdditionalInsurance',
      'ServiceconfigurationIC' => 'Dhl\\Bcs\\Api\\ServiceconfigurationIC',
      'Ident' => 'Dhl\\Bcs\\Api\\Ident',
      'ShipmentNotificationType' => 'Dhl\\Bcs\\Api\\ShipmentNotificationType',
      'BankType' => 'Dhl\\Bcs\\Api\\BankType',
      'ShipperType' => 'Dhl\\Bcs\\Api\\ShipperType',
      'ShipperTypeType' => 'Dhl\\Bcs\\Api\\ShipperTypeType',
      'NameType' => 'Dhl\\Bcs\\Api\\NameType',
      'NativeAddressType' => 'Dhl\\Bcs\\Api\\NativeAddressType',
      'CountryType' => 'Dhl\\Bcs\\Api\\CountryType',
      'CommunicationType' => 'Dhl\\Bcs\\Api\\CommunicationType',
      'ReceiverType' => 'Dhl\\Bcs\\Api\\ReceiverType',
      'ReceiverTypeType' => 'Dhl\\Bcs\\Api\\ReceiverTypeType',
      'ReceiverNativeAddressType' => 'Dhl\\Bcs\\Api\\ReceiverNativeAddressType',
      'cis:PackStationType' => 'Dhl\\Bcs\\Api\\PackStationType',
      'cis:PostfilialeType' => 'Dhl\\Bcs\\Api\\PostfilialeType',
      'cis:ParcelShopType' => 'Dhl\\Bcs\\Api\\ParcelShopType',
      'ExportDocumentType' => 'Dhl\\Bcs\\Api\\ExportDocumentType',
      'ExportDocPosition' => 'Dhl\\Bcs\\Api\\ExportDocPosition',
      'Statusinformation' => 'Dhl\\Bcs\\Api\\Statusinformation',
      'CreationState' => 'Dhl\\Bcs\\Api\\CreationState',
      'LabelData' => 'Dhl\\Bcs\\Api\\LabelData',
      'DeleteShipmentOrderRequest' => 'Dhl\\Bcs\\Api\\DeleteShipmentOrderRequest',
      'DeleteShipmentOrderResponse' => 'Dhl\\Bcs\\Api\\DeleteShipmentOrderResponse',
      'DeletionState' => 'Dhl\\Bcs\\Api\\DeletionState',
      'GetVersionResponse' => 'Dhl\\Bcs\\Api\\GetVersionResponse',
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

}
