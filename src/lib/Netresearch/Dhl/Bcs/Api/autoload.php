<?php


 function autoload_632246018bbf31fe53dcd3f7a85918d7($class)
{
    $classes = array(
        'Netresearch\Dhl\Bcs\Api\GVAPI_2_0_de' => __DIR__ . '/GVAPI_2_0_de.php',
        'Netresearch\Dhl\Bcs\Api\CreateShipmentOrderRequest' => __DIR__ . '/CreateShipmentOrderRequest.php',
        'Netresearch\Dhl\Bcs\Api\CreateShipmentOrderResponse' => __DIR__ . '/CreateShipmentOrderResponse.php',
        'Netresearch\Dhl\Bcs\Api\Version' => __DIR__ . '/Version.php',
        'Netresearch\Dhl\Bcs\Api\ShipmentOrderType' => __DIR__ . '/ShipmentOrderType.php',
        'Netresearch\Dhl\Bcs\Api\Shipment' => __DIR__ . '/Shipment.php',
        'Netresearch\Dhl\Bcs\Api\ShipmentDetailsTypeType' => __DIR__ . '/ShipmentDetailsTypeType.php',
        'Netresearch\Dhl\Bcs\Api\ShipmentDetailsType' => __DIR__ . '/ShipmentDetailsType.php',
        'Netresearch\Dhl\Bcs\Api\ShipmentItemType' => __DIR__ . '/ShipmentItemType.php',
        'Netresearch\Dhl\Bcs\Api\ShipmentService' => __DIR__ . '/ShipmentService.php',
        'Netresearch\Dhl\Bcs\Api\ServiceconfigurationDateOfDelivery' => __DIR__ . '/ServiceconfigurationDateOfDelivery.php',
        'Netresearch\Dhl\Bcs\Api\ServiceconfigurationDeliveryTimeframe' => __DIR__ . '/ServiceconfigurationDeliveryTimeframe.php',
        'Netresearch\Dhl\Bcs\Api\ServiceconfigurationISR' => __DIR__ . '/ServiceconfigurationISR.php',
        'Netresearch\Dhl\Bcs\Api\Serviceconfiguration' => __DIR__ . '/Serviceconfiguration.php',
        'Netresearch\Dhl\Bcs\Api\ServiceconfigurationShipmentHandling' => __DIR__ . '/ServiceconfigurationShipmentHandling.php',
        'Netresearch\Dhl\Bcs\Api\ServiceconfigurationEndorsement' => __DIR__ . '/ServiceconfigurationEndorsement.php',
        'Netresearch\Dhl\Bcs\Api\ServiceconfigurationVisualAgeCheck' => __DIR__ . '/ServiceconfigurationVisualAgeCheck.php',
        'Netresearch\Dhl\Bcs\Api\ServiceconfigurationDetails' => __DIR__ . '/ServiceconfigurationDetails.php',
        'Netresearch\Dhl\Bcs\Api\ServiceconfigurationCashOnDelivery' => __DIR__ . '/ServiceconfigurationCashOnDelivery.php',
        'Netresearch\Dhl\Bcs\Api\ServiceconfigurationAdditionalInsurance' => __DIR__ . '/ServiceconfigurationAdditionalInsurance.php',
        'Netresearch\Dhl\Bcs\Api\ServiceconfigurationIC' => __DIR__ . '/ServiceconfigurationIC.php',
        'Netresearch\Dhl\Bcs\Api\Ident' => __DIR__ . '/Ident.php',
        'Netresearch\Dhl\Bcs\Api\ShipmentNotificationType' => __DIR__ . '/ShipmentNotificationType.php',
        'Netresearch\Dhl\Bcs\Api\BankType' => __DIR__ . '/BankType.php',
        'Netresearch\Dhl\Bcs\Api\ShipperType' => __DIR__ . '/ShipperType.php',
        'Netresearch\Dhl\Bcs\Api\ShipperTypeType' => __DIR__ . '/ShipperTypeType.php',
        'Netresearch\Dhl\Bcs\Api\NameType' => __DIR__ . '/NameType.php',
        'Netresearch\Dhl\Bcs\Api\NativeAddressType' => __DIR__ . '/NativeAddressType.php',
        'Netresearch\Dhl\Bcs\Api\CountryType' => __DIR__ . '/CountryType.php',
        'Netresearch\Dhl\Bcs\Api\CommunicationType' => __DIR__ . '/CommunicationType.php',
        'Netresearch\Dhl\Bcs\Api\ReceiverType' => __DIR__ . '/ReceiverType.php',
        'Netresearch\Dhl\Bcs\Api\ReceiverTypeType' => __DIR__ . '/ReceiverTypeType.php',
        'Netresearch\Dhl\Bcs\Api\ReceiverNativeAddressType' => __DIR__ . '/ReceiverNativeAddressType.php',
        'Netresearch\Dhl\Bcs\Api\PackStationType' => __DIR__ . '/PackStationType.php',
        'Netresearch\Dhl\Bcs\Api\PostfilialeType' => __DIR__ . '/PostfilialeType.php',
        'Netresearch\Dhl\Bcs\Api\ParcelShopType' => __DIR__ . '/ParcelShopType.php',
        'Netresearch\Dhl\Bcs\Api\ExportDocumentType' => __DIR__ . '/ExportDocumentType.php',
        'Netresearch\Dhl\Bcs\Api\ExportDocPosition' => __DIR__ . '/ExportDocPosition.php',
        'Netresearch\Dhl\Bcs\Api\Statusinformation' => __DIR__ . '/Statusinformation.php',
        'Netresearch\Dhl\Bcs\Api\CreationState' => __DIR__ . '/CreationState.php',
        'Netresearch\Dhl\Bcs\Api\LabelData' => __DIR__ . '/LabelData.php',
        'Netresearch\Dhl\Bcs\Api\DeleteShipmentOrderRequest' => __DIR__ . '/DeleteShipmentOrderRequest.php',
        'Netresearch\Dhl\Bcs\Api\DeleteShipmentOrderResponse' => __DIR__ . '/DeleteShipmentOrderResponse.php',
        'Netresearch\Dhl\Bcs\Api\DeletionState' => __DIR__ . '/DeletionState.php',
        'Netresearch\Dhl\Bcs\Api\GetVersionResponse' => __DIR__ . '/GetVersionResponse.php'
    );
    if (!empty($classes[$class])) {
        include $classes[$class];
    };
}

spl_autoload_register('autoload_632246018bbf31fe53dcd3f7a85918d7');

// Do nothing. The rest is just leftovers from the code generation.
{
}
