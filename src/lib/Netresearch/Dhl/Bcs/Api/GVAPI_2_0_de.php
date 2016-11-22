<?php

namespace Netresearch\Dhl\Bcs\Api;

class GVAPI_2_0_de extends \SoapClient
{

    /**
     * @var array $classmap The defined classes
     */
    private static $classmap = array (
      'Version' => 'Netresearch\\Dhl\\Bcs\\Api\\Version',
      'GetVersionResponse' => 'Netresearch\\Dhl\\Bcs\\Api\\GetVersionResponse',
      'CreateShipmentOrderRequest' => 'Netresearch\\Dhl\\Bcs\\Api\\CreateShipmentOrderRequest',
      'CreateShipmentOrderResponse' => 'Netresearch\\Dhl\\Bcs\\Api\\CreateShipmentOrderResponse',
      'ShipmentOrderType' => 'Netresearch\\Dhl\\Bcs\\Api\\ShipmentOrderType',
      'Shipment' => 'Netresearch\\Dhl\\Bcs\\Api\\Shipment',
      'ShipmentDetailsTypeType' => 'Netresearch\\Dhl\\Bcs\\Api\\ShipmentDetailsTypeType',
      'ShipmentDetailsType' => 'Netresearch\\Dhl\\Bcs\\Api\\ShipmentDetailsType',
      'ShipmentItemType' => 'Netresearch\\Dhl\\Bcs\\Api\\ShipmentItemType',
      'ShipmentService' => 'Netresearch\\Dhl\\Bcs\\Api\\ShipmentService',
      'ServiceconfigurationDateOfDelivery' => 'Netresearch\\Dhl\\Bcs\\Api\\ServiceconfigurationDateOfDelivery',
      'ServiceconfigurationDeliveryTimeframe' => 'Netresearch\\Dhl\\Bcs\\Api\\ServiceconfigurationDeliveryTimeframe',
      'ServiceconfigurationISR' => 'Netresearch\\Dhl\\Bcs\\Api\\ServiceconfigurationISR',
      'Serviceconfiguration' => 'Netresearch\\Dhl\\Bcs\\Api\\Serviceconfiguration',
      'ServiceconfigurationShipmentHandling' => 'Netresearch\\Dhl\\Bcs\\Api\\ServiceconfigurationShipmentHandling',
      'ServiceconfigurationEndorsement' => 'Netresearch\\Dhl\\Bcs\\Api\\ServiceconfigurationEndorsement',
      'ServiceconfigurationVisualAgeCheck' => 'Netresearch\\Dhl\\Bcs\\Api\\ServiceconfigurationVisualAgeCheck',
      'ServiceconfigurationDetails' => 'Netresearch\\Dhl\\Bcs\\Api\\ServiceconfigurationDetails',
      'ServiceconfigurationCashOnDelivery' => 'Netresearch\\Dhl\\Bcs\\Api\\ServiceconfigurationCashOnDelivery',
      'ServiceconfigurationAdditionalInsurance' => 'Netresearch\\Dhl\\Bcs\\Api\\ServiceconfigurationAdditionalInsurance',
      'ServiceconfigurationIC' => 'Netresearch\\Dhl\\Bcs\\Api\\ServiceconfigurationIC',
      'Ident' => 'Netresearch\\Dhl\\Bcs\\Api\\Ident',
      'ShipmentNotificationType' => 'Netresearch\\Dhl\\Bcs\\Api\\ShipmentNotificationType',
      'BankType' => 'Netresearch\\Dhl\\Bcs\\Api\\BankType',
      'ShipperType' => 'Netresearch\\Dhl\\Bcs\\Api\\ShipperType',
      'ShipperTypeType' => 'Netresearch\\Dhl\\Bcs\\Api\\ShipperTypeType',
      'NameType' => 'Netresearch\\Dhl\\Bcs\\Api\\NameType',
      'NativeAddressType' => 'Netresearch\\Dhl\\Bcs\\Api\\NativeAddressType',
      'CountryType' => 'Netresearch\\Dhl\\Bcs\\Api\\CountryType',
      'CommunicationType' => 'Netresearch\\Dhl\\Bcs\\Api\\CommunicationType',
      'ReceiverType' => 'Netresearch\\Dhl\\Bcs\\Api\\ReceiverType',
      'ReceiverTypeType' => 'Netresearch\\Dhl\\Bcs\\Api\\ReceiverTypeType',
      'ReceiverNativeAddressType' => 'Netresearch\\Dhl\\Bcs\\Api\\ReceiverNativeAddressType',
      'cis:PackStationType' => 'Netresearch\\Dhl\\Bcs\\Api\\PackStationType',
      'cis:PostfilialeType' => 'Netresearch\\Dhl\\Bcs\\Api\\PostfilialeType',
      'cis:ParcelShopType' => 'Netresearch\\Dhl\\Bcs\\Api\\ParcelShopType',
      'ExportDocumentType' => 'Netresearch\\Dhl\\Bcs\\Api\\ExportDocumentType',
      'ExportDocPosition' => 'Netresearch\\Dhl\\Bcs\\Api\\ExportDocPosition',
      'Statusinformation' => 'Netresearch\\Dhl\\Bcs\\Api\\Statusinformation',
      'CreationState' => 'Netresearch\\Dhl\\Bcs\\Api\\CreationState',
      'LabelData' => 'Netresearch\\Dhl\\Bcs\\Api\\LabelData',
      'DeleteShipmentOrderRequest' => 'Netresearch\\Dhl\\Bcs\\Api\\DeleteShipmentOrderRequest',
      'DeleteShipmentOrderResponse' => 'Netresearch\\Dhl\\Bcs\\Api\\DeleteShipmentOrderResponse',
      'DeletionState' => 'Netresearch\\Dhl\\Bcs\\Api\\DeletionState',
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
