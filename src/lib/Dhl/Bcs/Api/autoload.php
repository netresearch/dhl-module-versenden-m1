<?php


 function autoload_632246018bbf31fe53dcd3f7a85918d7($class)
{
    $classes = array(
        'Dhl\Bcs\Api\GVAPI_2_0_de' => __DIR__ .'/GVAPI_2_0_de.php',
        'Dhl\Bcs\Api\CreateShipmentOrderRequest' => __DIR__ .'/CreateShipmentOrderRequest.php',
        'Dhl\Bcs\Api\CreateShipmentOrderResponse' => __DIR__ .'/CreateShipmentOrderResponse.php',
        'Dhl\Bcs\Api\Version' => __DIR__ .'/Version.php',
        'Dhl\Bcs\Api\ShipmentOrderType' => __DIR__ .'/ShipmentOrderType.php',
        'Dhl\Bcs\Api\Shipment' => __DIR__ .'/Shipment.php',
        'Dhl\Bcs\Api\ShipmentDetailsTypeType' => __DIR__ .'/ShipmentDetailsTypeType.php',
        'Dhl\Bcs\Api\ShipmentDetailsType' => __DIR__ .'/ShipmentDetailsType.php',
        'Dhl\Bcs\Api\ShipmentItemType' => __DIR__ .'/ShipmentItemType.php',
        'Dhl\Bcs\Api\ShipmentService' => __DIR__ .'/ShipmentService.php',
        'Dhl\Bcs\Api\ServiceconfigurationDateOfDelivery' => __DIR__ .'/ServiceconfigurationDateOfDelivery.php',
        'Dhl\Bcs\Api\ServiceconfigurationDeliveryTimeframe' => __DIR__ .'/ServiceconfigurationDeliveryTimeframe.php',
        'Dhl\Bcs\Api\ServiceconfigurationISR' => __DIR__ .'/ServiceconfigurationISR.php',
        'Dhl\Bcs\Api\Serviceconfiguration' => __DIR__ .'/Serviceconfiguration.php',
        'Dhl\Bcs\Api\ServiceconfigurationShipmentHandling' => __DIR__ .'/ServiceconfigurationShipmentHandling.php',
        'Dhl\Bcs\Api\ServiceconfigurationEndorsement' => __DIR__ .'/ServiceconfigurationEndorsement.php',
        'Dhl\Bcs\Api\ServiceconfigurationVisualAgeCheck' => __DIR__ .'/ServiceconfigurationVisualAgeCheck.php',
        'Dhl\Bcs\Api\ServiceconfigurationDetails' => __DIR__ .'/ServiceconfigurationDetails.php',
        'Dhl\Bcs\Api\ServiceconfigurationCashOnDelivery' => __DIR__ .'/ServiceconfigurationCashOnDelivery.php',
        'Dhl\Bcs\Api\ServiceconfigurationAdditionalInsurance' => __DIR__ .'/ServiceconfigurationAdditionalInsurance.php',
        'Dhl\Bcs\Api\ServiceconfigurationIC' => __DIR__ .'/ServiceconfigurationIC.php',
        'Dhl\Bcs\Api\Ident' => __DIR__ .'/Ident.php',
        'Dhl\Bcs\Api\ShipmentNotificationType' => __DIR__ .'/ShipmentNotificationType.php',
        'Dhl\Bcs\Api\BankType' => __DIR__ .'/BankType.php',
        'Dhl\Bcs\Api\ShipperType' => __DIR__ .'/ShipperType.php',
        'Dhl\Bcs\Api\ShipperTypeType' => __DIR__ .'/ShipperTypeType.php',
        'Dhl\Bcs\Api\NameType' => __DIR__ .'/NameType.php',
        'Dhl\Bcs\Api\NativeAddressType' => __DIR__ .'/NativeAddressType.php',
        'Dhl\Bcs\Api\CountryType' => __DIR__ .'/CountryType.php',
        'Dhl\Bcs\Api\CommunicationType' => __DIR__ .'/CommunicationType.php',
        'Dhl\Bcs\Api\ReceiverType' => __DIR__ .'/ReceiverType.php',
        'Dhl\Bcs\Api\ReceiverTypeType' => __DIR__ .'/ReceiverTypeType.php',
        'Dhl\Bcs\Api\ReceiverNativeAddressType' => __DIR__ .'/ReceiverNativeAddressType.php',
        'Dhl\Bcs\Api\PackStationType' => __DIR__ .'/PackStationType.php',
        'Dhl\Bcs\Api\PostfilialeType' => __DIR__ .'/PostfilialeType.php',
        'Dhl\Bcs\Api\ParcelShopType' => __DIR__ .'/ParcelShopType.php',
        'Dhl\Bcs\Api\ExportDocumentType' => __DIR__ .'/ExportDocumentType.php',
        'Dhl\Bcs\Api\ExportDocPosition' => __DIR__ .'/ExportDocPosition.php',
        'Dhl\Bcs\Api\Statusinformation' => __DIR__ .'/Statusinformation.php',
        'Dhl\Bcs\Api\CreationState' => __DIR__ .'/CreationState.php',
        'Dhl\Bcs\Api\LabelData' => __DIR__ .'/LabelData.php',
        'Dhl\Bcs\Api\DeleteShipmentOrderRequest' => __DIR__ .'/DeleteShipmentOrderRequest.php',
        'Dhl\Bcs\Api\DeleteShipmentOrderResponse' => __DIR__ .'/DeleteShipmentOrderResponse.php',
        'Dhl\Bcs\Api\DeletionState' => __DIR__ .'/DeletionState.php'
    );
    if (!empty($classes[$class])) {
        include $classes[$class];
    };
}

spl_autoload_register('autoload_632246018bbf31fe53dcd3f7a85918d7');

// Do nothing. The rest is just leftovers from the code generation.
{
}
