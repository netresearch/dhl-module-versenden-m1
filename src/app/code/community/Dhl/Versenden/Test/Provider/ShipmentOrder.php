<?php

/**
 * See LICENSE.md for license details.
 */

use \Dhl\Versenden\Bcs\Api\Webservice\RequestData;

class Dhl_Versenden_Test_Provider_ShipmentOrder
{
    public function provider()
    {
        $shipperAccountUser = 'Shipper Account User';
        $shipperAccountSignature = 'ABCDEF1234567890=';
        $shipperAccountEkp = '1234567890';
        $shipperAccountParticipation = array(
            \Dhl\Versenden\Bcs\Api\Product::PROCEDURE_PAKET_NATIONAL => '01',
            \Dhl\Versenden\Bcs\Api\Product::PROCEDURE_KLEINPAKET => '01',
            \Dhl\Versenden\Bcs\Api\Product::PROCEDURE_WELTPAKET => '01',
            \Dhl\Versenden\Bcs\Api\Product::PROCEDURE_RETURNSHIPMENT_NATIONAL => '01',
        );

        $shipperBankDataAccountOwner = 'Shipper BankData Account Owner';
        $shipperBankDataBankName = 'Shipper BankData Bank Name';
        $shipperBankDataIban = 'XX1234567890';
        $shipperBankDataBic = 'CC123456789';
        $shipperBankDataNote1 = 'Shipper BankData Note 1';
        $shipperBankDataNote2 = 'Shipper BankData Note 2';
        $shipperBankDataAccountReference = 'Shipper BankData Ref';

        $shipperContactName1 = 'Shipper Contact Name 1';
        $shipperContactName2 = 'Shipper Contact Name 2';
        $shipperContactName3 = 'Shipper Contact Name 3';
        $shipperContactStreetName = 'Shipper Contact Street Name';
        $shipperContactStreetNumber = 'SN 1a';
        $shipperContactAddressAddition = 'Shipper Contact Address Addition';
        $shipperContactDispatchingInformation = 'Shipper Contact Dispatching Info';
        $shipperContactZip = 'XX123';
        $shipperContactCity = 'Shipper Contact City';
        $shipperContactCountry = 'Shipper Contact Country';
        $shipperContactCountryISOCode = 'XX';
        $shipperContactState = 'Shipper Contact State';
        $shipperContactPhone = '+99 99999';
        $shipperContactEmail = 'Shipper Contact Email';
        $shipperContactContactPerson = 'Shipper Contact Person';

        $shipperReturnReceiverName1 = 'Return Receiver Name 1';
        $shipperReturnReceiverName2 = 'Return Receiver Name 2';
        $shipperReturnReceiverName3 = 'Return Receiver Name 3';
        $shipperReturnReceiverStreetName = 'Return Receiver Street Name';
        $shipperReturnReceiverStreetNumber = 'SN 1b';
        $shipperReturnReceiverAddressAddition = 'Return Receiver Address Addition';
        $shipperReturnReceiverDispatchingInformation = 'Return Receiver Dispatching Info';
        $shipperReturnReceiverZip = 'XX456';
        $shipperReturnReceiverCity = 'Return Receiver City';
        $shipperReturnReceiverCountry = 'Return Receiver Country';
        $shipperReturnReceiverCountryISOCode = 'YY';
        $shipperReturnReceiverState = 'Return Receiver State';
        $shipperReturnReceiverPhone = '+77 77777';
        $shipperReturnReceiverEmail = 'Return Receiver Email';
        $shipperReturnReceiverContactPerson = 'Return Receiver Person';

        $receiverName1 = 'Receiver Name 1';
        $receiverName2 = 'Receiver Name 2';
        $receiverName3 = 'Receiver Name 3';
        $receiverStreetName = 'Receiver Street Name';
        $receiverStreetNumber = 'SN 1c';
        $receiverAddressAddition = 'Receiver Address Addition';
        $receiverDispatchingInformation = 'Receiver Dispatching Info';
        $receiverZip = 'YY123';
        $receiverCity = 'Receiver City';
        $receiverCountry = 'Receiver Country';
        $receiverCountryISOCode = 'YY';
        $receiverState = 'Receiver State';
        $receiverPhone = '+55 55555';
        $receiverEmail = 'Receiver Email';
        $receiverContactPerson = 'Receiver Person';
        $packstationZip = 'PP123';
        $packstationCity = 'Packstation City';
        $packstationPackstationNumber = 'Packstation 808';
        $packstationPostNumber = '654321';

        $globalSettingsLabelType = 'R2D2';

        $packageId = '1';

        $shipmentSettingsDate = '2012-12-12';
        $shipmentSettingsReference = 'Foo Ref';
        $shipmentSettingsWeight = 2.4;
        $shipmentSettingsProduct = \Dhl\Versenden\Bcs\Api\Product::CODE_WELTPAKET;

        $exportInvoiceNumber = '103000002';
        $exportType = Dhl_Versenden_Model_Shipping_Carrier_Versenden::EXPORT_TYPE_DOCUMENT;
        $exportTypeDescription = '';
        $exportTermsOfTrade = Dhl_Versenden_Model_Shipping_Carrier_Versenden::TOT_DDX;
        $exportAdditionalFee = 2.95;
        $exportPlaceOfCommittal = 'Postfiliale 520, 04229 Leipzig, GERMANY';
        $exportPermitNumber = '';
        $exportAttestationNumber = '';
        $exportElectronicNotification = true;
        $exportPositionDescription = 'Socks';
        $exportPositionOrigin = 'TR';
        $exportPositionTarriffNumber = '61159500';

        $serviceSettingsDayOfDelivery = '2012-12-24';
        $serviceSettingsDeliveryTimeFrame = '19002100';
        $serviceSettingsPreferredLocation = 'Chimney';
        $serviceSettingsPreferredNeighbour = 'Santa Berger';
        $serviceSettingsParcelAnnouncement = true;
        $serviceSettingsVisualCheckOfAge = 'A21';
        $serviceSettingsReturnShipment = true;
        $serviceSettingsInsurance = 'B';
        $serviceSettingsBulkyGoods = true;
        $serviceSettingsParcelOutletRouting = 'Receiver Routing Contact';
        $serviceSettingsCod = false;
        $serviceSettingsPrintOnlyIfCodeable = true;

        $sequenceNumber = 77;
        $labelResponseType = $globalSettingsLabelType;

        $expectation = new Dhl_Versenden_Test_Expectation_ShipmentOrder(
            $shipperAccountUser, $shipperAccountSignature, $shipperAccountEkp,
            $shipperAccountParticipation,

            $shipperBankDataAccountOwner, $shipperBankDataBankName, $shipperBankDataIban,
            $shipperBankDataBic, $shipperBankDataNote1, $shipperBankDataNote2,
            $shipperBankDataAccountReference,

            $shipperContactName1, $shipperContactName2, $shipperContactName3,
            $shipperContactStreetName, $shipperContactStreetNumber,
            $shipperContactAddressAddition, $shipperContactDispatchingInformation,
            $shipperContactZip, $shipperContactCity, $shipperContactCountry,
            $shipperContactCountryISOCode, $shipperContactState, $shipperContactPhone,
            $shipperContactEmail, $shipperContactContactPerson,

            $shipperReturnReceiverName1, $shipperReturnReceiverName2, $shipperReturnReceiverName3,
            $shipperReturnReceiverStreetName, $shipperReturnReceiverStreetNumber,
            $shipperReturnReceiverAddressAddition, $shipperReturnReceiverDispatchingInformation,
            $shipperReturnReceiverZip, $shipperReturnReceiverCity, $shipperReturnReceiverCountry,
            $shipperReturnReceiverCountryISOCode, $shipperReturnReceiverState,
            $shipperReturnReceiverPhone, $shipperReturnReceiverEmail,
            $shipperReturnReceiverContactPerson,

            $receiverName1, $receiverName2, $receiverName3, $receiverStreetName,
            $receiverStreetNumber, $receiverAddressAddition, $receiverDispatchingInformation,
            $receiverZip, $receiverCity, $receiverCountry, $receiverCountryISOCode,
            $receiverState, $receiverPhone, $receiverEmail, $receiverContactPerson,
            $packstationZip, $packstationCity, $packstationPackstationNumber, $packstationPostNumber,

            $globalSettingsLabelType,

            $shipmentSettingsDate, $shipmentSettingsReference, $shipmentSettingsWeight,
            $shipmentSettingsProduct, $serviceSettingsDayOfDelivery,
            $serviceSettingsDeliveryTimeFrame, $serviceSettingsPreferredLocation,
            $serviceSettingsPreferredNeighbour, $serviceSettingsParcelAnnouncement,
            $serviceSettingsVisualCheckOfAge, $serviceSettingsReturnShipment,
            $serviceSettingsInsurance, $serviceSettingsBulkyGoods, $serviceSettingsParcelOutletRouting,
            $serviceSettingsCod, $serviceSettingsPrintOnlyIfCodeable,

            $sequenceNumber, $labelResponseType
        );

        $shipperAccount = new RequestData\ShipmentOrder\Shipper\Account(
            $shipperAccountUser, $shipperAccountSignature, $shipperAccountEkp,
            $shipperAccountParticipation
        );
        $shipperBankData = new RequestData\ShipmentOrder\Shipper\BankData(
            $shipperBankDataAccountOwner, $shipperBankDataBankName,
            $shipperBankDataIban, $shipperBankDataBic,
            $shipperBankDataNote1, $shipperBankDataNote2,
            $shipperBankDataAccountReference
        );
        $shipperContact = new RequestData\ShipmentOrder\Shipper\Contact(
            $shipperContactName1, $shipperContactName2, $shipperContactName3,
            $shipperContactStreetName, $shipperContactStreetNumber,
            $shipperContactAddressAddition, $shipperContactDispatchingInformation,
            $shipperContactZip, $shipperContactCity, $shipperContactCountry,
            $shipperContactCountryISOCode, $shipperContactState, $shipperContactPhone,
            $shipperContactEmail, $shipperContactContactPerson
        );
        $shipperReturnReceiver = new RequestData\ShipmentOrder\Shipper\ReturnReceiver(
            $shipperReturnReceiverName1, $shipperReturnReceiverName2, $shipperReturnReceiverName3,
            $shipperReturnReceiverStreetName, $shipperReturnReceiverStreetNumber,
            $shipperReturnReceiverAddressAddition, $shipperReturnReceiverDispatchingInformation,
            $shipperReturnReceiverZip, $shipperReturnReceiverCity, $shipperReturnReceiverCountry,
            $shipperReturnReceiverCountryISOCode, $shipperReturnReceiverState, $shipperReturnReceiverPhone,
            $shipperReturnReceiverEmail, $shipperReturnReceiverContactPerson
        );

        $shipper = new RequestData\ShipmentOrder\Shipper(
            $shipperAccount,
            $shipperBankData,
            $shipperContact,
            $shipperReturnReceiver
        );

        $packstation = new RequestData\ShipmentOrder\Receiver\Packstation(
            $packstationZip, $packstationCity,
            $receiverCountry, $receiverCountryISOCode, $receiverState,
            $packstationPackstationNumber, $packstationPostNumber
        );
        $receiver = new RequestData\ShipmentOrder\Receiver(
            $receiverName1, $receiverName2, $receiverName3, $receiverStreetName,
            $receiverStreetNumber, $receiverAddressAddition,
            $receiverDispatchingInformation, $receiverZip, $receiverCity,
            $receiverCountry, $receiverCountryISOCode, $receiverState,
            $receiverPhone, $receiverEmail, $receiverContactPerson, $packstation
        );

        $packageCollection = new RequestData\ShipmentOrder\PackageCollection();
        $package = new RequestData\ShipmentOrder\Package(0, $shipmentSettingsWeight);
        $packageCollection->addItem($package);

        $exportPositionCollection = new RequestData\ShipmentOrder\Export\PositionCollection();
        $exportPosition = new RequestData\ShipmentOrder\Export\Position(
            1,
            $exportPositionDescription,
            $exportPositionOrigin,
            $exportPositionTarriffNumber,
            2,
            0.400,
            9.95
        );
        $exportPositionCollection->addItem($exportPosition);

        $exportDocumentCollection = new RequestData\ShipmentOrder\Export\DocumentCollection();
        $exportDocument = new RequestData\ShipmentOrder\Export\Document(
            $packageId,
            $exportInvoiceNumber,
            $exportType,
            $exportTypeDescription,
            $exportTermsOfTrade,
            $exportAdditionalFee,
            $exportPlaceOfCommittal,
            $exportPermitNumber,
            $exportAttestationNumber,
            $exportElectronicNotification,
            $exportPositionCollection
        );
        $exportDocumentCollection->addItem($exportDocument);

        $serviceSelection = new RequestData\ShipmentOrder\ServiceSelection(
            $serviceSettingsDayOfDelivery, $serviceSettingsDeliveryTimeFrame,
            $serviceSettingsPreferredLocation, $serviceSettingsPreferredNeighbour,
            $serviceSettingsParcelAnnouncement,
            $serviceSettingsVisualCheckOfAge, $serviceSettingsReturnShipment,
            $serviceSettingsInsurance, $serviceSettingsBulkyGoods, $serviceSettingsParcelOutletRouting,
            $serviceSettingsCod, $serviceSettingsPrintOnlyIfCodeable
        );



        $shipmentOrder = new RequestData\ShipmentOrder(
            $sequenceNumber,
            $shipmentSettingsReference,
            $shipper,
            $receiver,
            $serviceSelection,
            $packageCollection,
            $exportDocumentCollection,
            $shipmentSettingsProduct,
            $shipmentSettingsDate,
            $labelResponseType
        );

        return [[$shipmentOrder, $expectation]];
    }
}
