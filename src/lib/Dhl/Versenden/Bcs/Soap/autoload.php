<?php


 function autoload_cb7a1e3c1c35d7211bbe881b51042ac1($class)
{
    $classes = array(
        'Dhl\Versenden\Bcs\Soap\GVAPI_2_0_de' => __DIR__ .'/GVAPI_2_0_de.php',
        'Dhl\Versenden\Bcs\Soap\Version' => __DIR__ .'/Version.php',
        'Dhl\Versenden\Bcs\Soap\GetVersionResponse' => __DIR__ .'/GetVersionResponse.php',
        'Dhl\Versenden\Bcs\Soap\CreateShipmentOrderRequest' => __DIR__ .'/CreateShipmentOrderRequest.php',
        'Dhl\Versenden\Bcs\Soap\CreateShipmentOrderResponse' => __DIR__ .'/CreateShipmentOrderResponse.php',
        'Dhl\Versenden\Bcs\Soap\ShipmentOrderType' => __DIR__ .'/ShipmentOrderType.php',
        'Dhl\Versenden\Bcs\Soap\Shipment' => __DIR__ .'/Shipment.php',
        'Dhl\Versenden\Bcs\Soap\ShipmentDetailsTypeType' => __DIR__ .'/ShipmentDetailsTypeType.php',
        'Dhl\Versenden\Bcs\Soap\ShipmentDetailsType' => __DIR__ .'/ShipmentDetailsType.php',
        'Dhl\Versenden\Bcs\Soap\ShipmentItemType' => __DIR__ .'/ShipmentItemType.php',
        'Dhl\Versenden\Bcs\Soap\ShipmentService' => __DIR__ .'/ShipmentService.php',
        'Dhl\Versenden\Bcs\Soap\ServiceconfigurationDateOfDelivery' => __DIR__ .'/ServiceconfigurationDateOfDelivery.php',
        'Dhl\Versenden\Bcs\Soap\ServiceconfigurationDeliveryTimeframe' => __DIR__ .'/ServiceconfigurationDeliveryTimeframe.php',
        'Dhl\Versenden\Bcs\Soap\ServiceconfigurationISR' => __DIR__ .'/ServiceconfigurationISR.php',
        'Dhl\Versenden\Bcs\Soap\Serviceconfiguration' => __DIR__ .'/Serviceconfiguration.php',
        'Dhl\Versenden\Bcs\Soap\ServiceconfigurationShipmentHandling' => __DIR__ .'/ServiceconfigurationShipmentHandling.php',
        'Dhl\Versenden\Bcs\Soap\ServiceconfigurationEndorsement' => __DIR__ .'/ServiceconfigurationEndorsement.php',
        'Dhl\Versenden\Bcs\Soap\ServiceconfigurationVisualAgeCheck' => __DIR__ .'/ServiceconfigurationVisualAgeCheck.php',
        'Dhl\Versenden\Bcs\Soap\ServiceconfigurationDetails' => __DIR__ .'/ServiceconfigurationDetails.php',
        'Dhl\Versenden\Bcs\Soap\ServiceconfigurationCashOnDelivery' => __DIR__ .'/ServiceconfigurationCashOnDelivery.php',
        'Dhl\Versenden\Bcs\Soap\ServiceconfigurationAdditionalInsurance' => __DIR__ .'/ServiceconfigurationAdditionalInsurance.php',
        'Dhl\Versenden\Bcs\Soap\ServiceconfigurationIC' => __DIR__ .'/ServiceconfigurationIC.php',
        'Dhl\Versenden\Bcs\Soap\Ident' => __DIR__ .'/Ident.php',
        'Dhl\Versenden\Bcs\Soap\ShipmentNotificationType' => __DIR__ .'/ShipmentNotificationType.php',
        'Dhl\Versenden\Bcs\Soap\BankType' => __DIR__ .'/BankType.php',
        'Dhl\Versenden\Bcs\Soap\ShipperType' => __DIR__ .'/ShipperType.php',
        'Dhl\Versenden\Bcs\Soap\ShipperTypeType' => __DIR__ .'/ShipperTypeType.php',
        'Dhl\Versenden\Bcs\Soap\NameType' => __DIR__ .'/NameType.php',
        'Dhl\Versenden\Bcs\Soap\NativeAddressType' => __DIR__ .'/NativeAddressType.php',
        'Dhl\Versenden\Bcs\Soap\CountryType' => __DIR__ .'/CountryType.php',
        'Dhl\Versenden\Bcs\Soap\CommunicationType' => __DIR__ .'/CommunicationType.php',
        'Dhl\Versenden\Bcs\Soap\ReceiverType' => __DIR__ .'/ReceiverType.php',
        'Dhl\Versenden\Bcs\Soap\ReceiverTypeType' => __DIR__ .'/ReceiverTypeType.php',
        'Dhl\Versenden\Bcs\Soap\ReceiverNativeAddressType' => __DIR__ .'/ReceiverNativeAddressType.php',
        'Dhl\Versenden\Bcs\Soap\PackStationType' => __DIR__ .'/PackStationType.php',
        'Dhl\Versenden\Bcs\Soap\PostfilialeType' => __DIR__ .'/PostfilialeType.php',
        'Dhl\Versenden\Bcs\Soap\ParcelShopType' => __DIR__ .'/ParcelShopType.php',
        'Dhl\Versenden\Bcs\Soap\ExportDocumentType' => __DIR__ .'/ExportDocumentType.php',
        'Dhl\Versenden\Bcs\Soap\ExportDocPosition' => __DIR__ .'/ExportDocPosition.php',
        'Dhl\Versenden\Bcs\Soap\Statusinformation' => __DIR__ .'/Statusinformation.php',
        'Dhl\Versenden\Bcs\Soap\CreationState' => __DIR__ .'/CreationState.php',
        'Dhl\Versenden\Bcs\Soap\LabelData' => __DIR__ .'/LabelData.php',
        'Dhl\Versenden\Bcs\Soap\DeleteShipmentOrderRequest' => __DIR__ .'/DeleteShipmentOrderRequest.php',
        'Dhl\Versenden\Bcs\Soap\DeleteShipmentOrderResponse' => __DIR__ .'/DeleteShipmentOrderResponse.php',
        'Dhl\Versenden\Bcs\Soap\DeletionState' => __DIR__ .'/DeletionState.php'
    );
    if (!empty($classes[$class])) {
        include $classes[$class];
    };
}

spl_autoload_register('autoload_cb7a1e3c1c35d7211bbe881b51042ac1');

// Do nothing. The rest is just leftovers from the code generation.
{
}
