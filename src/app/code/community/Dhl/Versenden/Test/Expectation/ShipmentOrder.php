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
 * @package   Dhl_Versenden
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */

/**
 * Dhl_Versenden_Test_Expectation_ShipmentOrder
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Test_Expectation_ShipmentOrder
{
    /** @var string */
    protected $shipperAccountUser;
    /** @var string */
    protected $shipperAccountSignature;
    /** @var string */
    protected $shipperAccountEkp;
    /** @var string[] */
    protected $shipperAccountParticipations;

    /** @var string */
    protected $shipperBankDataAccountOwner;
    /** @var string */
    protected $shipperBankDataBankName;
    /** @var string */
    protected $shipperBankDataIban;
    /** @var string */
    protected $shipperBankDataBic;
    /** @var string */
    protected $shipperBankDataNote1;
    /** @var string */
    protected $shipperBankDataNote2;
    /** @var string */
    protected $shipperBankDataAccountReference;

    protected $shipperContactName1;
    /** @var string */
    protected $shipperContactName2;
    /** @var string */
    protected $shipperContactName3;
    /** @var string */
    protected $shipperContactStreetName;
    /** @var string */
    protected $shipperContactStreetNumber;
    /** @var string */
    protected $shipperContactAddressAddition;
    /** @var string */
    protected $shipperContactDispatchingInformation;
    /** @var string */
    protected $shipperContactZip;
    /** @var string */
    protected $shipperContactCity;
    /** @var string */
    protected $shipperContactCountry;
    /** @var string */
    protected $shipperContactCountryISOCode;
    /** @var string */
    protected $shipperContactState;
    /** @var string */
    protected $shipperContactPhone;
    /** @var string */
    protected $shipperContactEmail;
    /** @var string */
    protected $shipperContactContactPerson;

    protected $shipperReturnReceiverName1;
    /** @var string */
    protected $shipperReturnReceiverName2;
    /** @var string */
    protected $shipperReturnReceiverName3;
    /** @var string */
    protected $shipperReturnReceiverStreetName;
    /** @var string */
    protected $shipperReturnReceiverStreetNumber;
    /** @var string */
    protected $shipperReturnReceiverAddressAddition;
    /** @var string */
    protected $shipperReturnReceiverDispatchingInformation;
    /** @var string */
    protected $shipperReturnReceiverZip;
    /** @var string */
    protected $shipperReturnReceiverCity;
    /** @var string */
    protected $shipperReturnReceiverCountry;
    /** @var string */
    protected $shipperReturnReceiverCountryISOCode;
    /** @var string */
    protected $shipperReturnReceiverState;
    /** @var string */
    protected $shipperReturnReceiverPhone;
    /** @var string */
    protected $shipperReturnReceiverEmail;
    /** @var string */
    protected $shipperReturnReceiverContactPerson;

    protected $receiverName1;
    /** @var string */
    protected $receiverName2;
    /** @var string */
    protected $receiverName3;
    /** @var string */
    protected $receiverStreetName;
    /** @var string */
    protected $receiverStreetNumber;
    /** @var string */
    protected $receiverAddressAddition;
    /** @var string */
    protected $receiverDispatchingInformation;
    /** @var string */
    protected $receiverZip;
    /** @var string */
    protected $receiverCity;
    /** @var string */
    protected $receiverCountry;
    /** @var string */
    protected $receiverCountryISOCode;
    /** @var string */
    protected $receiverState;
    /** @var string */
    protected $receiverPhone;
    /** @var string */
    protected $receiverEmail;
    /** @var string */
    protected $receiverContactPerson;

    /** @var string */
    protected $packstationZip;
    /** @var string */
    protected $packstationCity;
    /** @var string */
    protected $packstationPackstationNumber;
    /** @var string */
    protected $packstationPostNumber;

    /** @var string */
    protected $globalSettingsLabelType;

    /** @var string */
    protected $shipmentSettingsDate;
    /** @var string */
    protected $shipmentSettingsReference;
    /** @var float */
    protected $shipmentSettingsWeight;
    /** @var string */
    protected $shipmentSettingsProduct;

    protected $serviceSettingsPreferredDay;
    /** @var bool|string false or time */
    protected $serviceSettingsPreferredTime;
    /** @var bool|string false or location */
    protected $serviceSettingsPreferredLocation;
    /** @var bool|string false or neighbour address */
    protected $serviceSettingsPreferredNeighbour;
    /** @var int yes/no/optional */
    protected $serviceSettingsParcelAnnouncement;
    /** @var bool|string false or A16 or A18 */
    protected $serviceSettingsVisualCheckOfAge;
    /** @var bool false or true */
    protected $serviceSettingsReturnShipment;
    /** @var bool|float false or order total */
    protected $serviceSettingsInsurance;
    /** @var bool false or true */
    protected $serviceSettingsBulkyGoods;
    /** @var bool|string false or customer email */
    protected $serviceSettingsParcelOutletRouting;
    /** @var bool|float false or order total */
    protected $serviceSettingsCod;
    /** @var bool false or true */
    protected $serviceSettingsPrintOnlyIfCodeable;

    /** @var int */
    protected $sequenceNumber;
    /** @var string */
    protected $labelResponseType;

    /**
     * Dhl_Versenden_Test_Expectation_ShipmentOrder constructor.
     * @param string $shipperAccountUser
     * @param string $shipperAccountSignature
     * @param string $shipperAccountEkp
     * @param string[] $shipperAccountParticipations
     * @param string $shipperBankDataAccountOwner
     * @param string $shipperBankDataBankName
     * @param string $shipperBankDataIban
     * @param string $shipperBankDataBic
     * @param string $shipperBankDataNote1
     * @param string $shipperBankDataNote2
     * @param string $shipperBankDataAccountReference
     * @param $shipperContactName1
     * @param string $shipperContactName2
     * @param string $shipperContactName3
     * @param string $shipperContactStreetName
     * @param string $shipperContactStreetNumber
     * @param string $shipperContactAddressAddition
     * @param string $shipperContactDispatchingInformation
     * @param string $shipperContactZip
     * @param string $shipperContactCity
     * @param string $shipperContactCountry
     * @param string $shipperContactCountryISOCode
     * @param string $shipperContactState
     * @param string $shipperContactPhone
     * @param string $shipperContactEmail
     * @param string $shipperContactContactPerson
     * @param $shipperReturnReceiverName1
     * @param string $shipperReturnReceiverName2
     * @param string $shipperReturnReceiverName3
     * @param string $shipperReturnReceiverStreetName
     * @param string $shipperReturnReceiverStreetNumber
     * @param string $shipperReturnReceiverAddressAddition
     * @param string $shipperReturnReceiverDispatchingInformation
     * @param string $shipperReturnReceiverZip
     * @param string $shipperReturnReceiverCity
     * @param string $shipperReturnReceiverCountry
     * @param string $shipperReturnReceiverCountryISOCode
     * @param string $shipperReturnReceiverState
     * @param string $shipperReturnReceiverPhone
     * @param string $shipperReturnReceiverEmail
     * @param string $shipperReturnReceiverContactPerson
     * @param $receiverName1
     * @param string $receiverName2
     * @param string $receiverName3
     * @param string $receiverStreetName
     * @param string $receiverStreetNumber
     * @param string $receiverAddressAddition
     * @param string $receiverDispatchingInformation
     * @param string $receiverZip
     * @param string $receiverCity
     * @param string $receiverCountry
     * @param string $receiverCountryISOCode
     * @param string $receiverState
     * @param string $receiverPhone
     * @param string $receiverEmail
     * @param string $receiverContactPerson
     * @param string $packstationZip
     * @param string $packstationCity
     * @param string $packstationPackstationNumber
     * @param string $packstationPostNumber
     * @param string $globalSettingsLabelType
     * @param string $shipmentSettingsDate
     * @param string $shipmentSettingsReference
     * @param float $shipmentSettingsWeight
     * @param string $shipmentSettingsProduct
     * @param $serviceSettingsPreferredDay
     * @param bool|string $serviceSettingsPreferredTime
     * @param bool|string $serviceSettingsPreferredLocation
     * @param bool|string $serviceSettingsPreferredNeighbour
     * @param int $serviceSettingsParcelAnnouncement
     * @param bool|string $serviceSettingsVisualCheckOfAge
     * @param bool $serviceSettingsReturnShipment
     * @param bool|float $serviceSettingsInsurance
     * @param bool $serviceSettingsBulkyGoods
     * @param bool|string $serviceSettingsParcelOutletRouting
     * @param bool|float $serviceSettingsCod
     * @param bool $serviceSettingsPrintOnlyIfCodeable
     * @param int $sequenceNumber
     * @param string $labelResponseType
     */
    public function __construct(
        $shipperAccountUser, $shipperAccountSignature, $shipperAccountEkp,
        $shipperAccountParticipations,

        $shipperBankDataAccountOwner, $shipperBankDataBankName, $shipperBankDataIban,
        $shipperBankDataBic, $shipperBankDataNote1, $shipperBankDataNote2,
        $shipperBankDataAccountReference,

        $shipperContactName1, $shipperContactName2, $shipperContactName3,
        $shipperContactStreetName, $shipperContactStreetNumber, $shipperContactAddressAddition,
        $shipperContactDispatchingInformation, $shipperContactZip,
        $shipperContactCity, $shipperContactCountry, $shipperContactCountryISOCode,
        $shipperContactState, $shipperContactPhone, $shipperContactEmail,
        $shipperContactContactPerson, $shipperReturnReceiverName1,

        $shipperReturnReceiverName2, $shipperReturnReceiverName3,
        $shipperReturnReceiverStreetName, $shipperReturnReceiverStreetNumber,
        $shipperReturnReceiverAddressAddition,
        $shipperReturnReceiverDispatchingInformation, $shipperReturnReceiverZip,
        $shipperReturnReceiverCity, $shipperReturnReceiverCountry,
        $shipperReturnReceiverCountryISOCode, $shipperReturnReceiverState,
        $shipperReturnReceiverPhone, $shipperReturnReceiverEmail,
        $shipperReturnReceiverContactPerson,

        $receiverName1, $receiverName2, $receiverName3, $receiverStreetName,
        $receiverStreetNumber, $receiverAddressAddition,
        $receiverDispatchingInformation, $receiverZip, $receiverCity,
        $receiverCountry, $receiverCountryISOCode, $receiverState,
        $receiverPhone, $receiverEmail, $receiverContactPerson, $packstationZip,
        $packstationCity, $packstationPackstationNumber, $packstationPostNumber,

        $globalSettingsLabelType,

        $shipmentSettingsDate, $shipmentSettingsReference, $shipmentSettingsWeight, $shipmentSettingsProduct,

        $serviceSettingsPreferredDay, $serviceSettingsPreferredTime,
        $serviceSettingsPreferredLocation, $serviceSettingsPreferredNeighbour,
        $serviceSettingsParcelAnnouncement, $serviceSettingsVisualCheckOfAge,
        $serviceSettingsReturnShipment, $serviceSettingsInsurance, $serviceSettingsBulkyGoods,
        $serviceSettingsParcelOutletRouting, $serviceSettingsCod, $serviceSettingsPrintOnlyIfCodeable,

        $sequenceNumber, $labelResponseType
    ) {
        $this->shipperAccountUser = $shipperAccountUser;
        $this->shipperAccountSignature = $shipperAccountSignature;
        $this->shipperAccountEkp = $shipperAccountEkp;
        $this->shipperAccountParticipations = $shipperAccountParticipations;
        $this->shipperBankDataAccountOwner = $shipperBankDataAccountOwner;
        $this->shipperBankDataBankName = $shipperBankDataBankName;
        $this->shipperBankDataIban = $shipperBankDataIban;
        $this->shipperBankDataBic = $shipperBankDataBic;
        $this->shipperBankDataNote1 = $shipperBankDataNote1;
        $this->shipperBankDataNote2 = $shipperBankDataNote2;
        $this->shipperBankDataAccountReference = $shipperBankDataAccountReference;
        $this->shipperContactName1 = $shipperContactName1;
        $this->shipperContactName2 = $shipperContactName2;
        $this->shipperContactName3 = $shipperContactName3;
        $this->shipperContactStreetName = $shipperContactStreetName;
        $this->shipperContactStreetNumber = $shipperContactStreetNumber;
        $this->shipperContactAddressAddition = $shipperContactAddressAddition;
        $this->shipperContactDispatchingInformation = $shipperContactDispatchingInformation;
        $this->shipperContactZip = $shipperContactZip;
        $this->shipperContactCity = $shipperContactCity;
        $this->shipperContactCountry = $shipperContactCountry;
        $this->shipperContactCountryISOCode = $shipperContactCountryISOCode;
        $this->shipperContactState = $shipperContactState;
        $this->shipperContactPhone = $shipperContactPhone;
        $this->shipperContactEmail = $shipperContactEmail;
        $this->shipperContactContactPerson = $shipperContactContactPerson;
        $this->shipperReturnReceiverName1 = $shipperReturnReceiverName1;
        $this->shipperReturnReceiverName2 = $shipperReturnReceiverName2;
        $this->shipperReturnReceiverName3 = $shipperReturnReceiverName3;
        $this->shipperReturnReceiverStreetName = $shipperReturnReceiverStreetName;
        $this->shipperReturnReceiverStreetNumber = $shipperReturnReceiverStreetNumber;
        $this->shipperReturnReceiverAddressAddition = $shipperReturnReceiverAddressAddition;
        $this->shipperReturnReceiverDispatchingInformation = $shipperReturnReceiverDispatchingInformation;
        $this->shipperReturnReceiverZip = $shipperReturnReceiverZip;
        $this->shipperReturnReceiverCity = $shipperReturnReceiverCity;
        $this->shipperReturnReceiverCountry = $shipperReturnReceiverCountry;
        $this->shipperReturnReceiverCountryISOCode = $shipperReturnReceiverCountryISOCode;
        $this->shipperReturnReceiverState = $shipperReturnReceiverState;
        $this->shipperReturnReceiverPhone = $shipperReturnReceiverPhone;
        $this->shipperReturnReceiverEmail = $shipperReturnReceiverEmail;
        $this->shipperReturnReceiverContactPerson = $shipperReturnReceiverContactPerson;
        $this->receiverName1 = $receiverName1;
        $this->receiverName2 = $receiverName2;
        $this->receiverName3 = $receiverName3;
        $this->receiverStreetName = $receiverStreetName;
        $this->receiverStreetNumber = $receiverStreetNumber;
        $this->receiverAddressAddition = $receiverAddressAddition;
        $this->receiverDispatchingInformation = $receiverDispatchingInformation;
        $this->receiverZip = $receiverZip;
        $this->receiverCity = $receiverCity;
        $this->receiverCountry = $receiverCountry;
        $this->receiverCountryISOCode = $receiverCountryISOCode;
        $this->receiverState = $receiverState;
        $this->receiverPhone = $receiverPhone;
        $this->receiverEmail = $receiverEmail;
        $this->receiverContactPerson = $receiverContactPerson;
        $this->packstationZip = $packstationZip;
        $this->packstationCity = $packstationCity;
        $this->packstationPackstationNumber = $packstationPackstationNumber;
        $this->packstationPostNumber = $packstationPostNumber;
        $this->globalSettingsLabelType = $globalSettingsLabelType;
        $this->shipmentSettingsDate = $shipmentSettingsDate;
        $this->shipmentSettingsReference = $shipmentSettingsReference;
        $this->shipmentSettingsWeight = $shipmentSettingsWeight;
        $this->shipmentSettingsProduct = $shipmentSettingsProduct;
        $this->serviceSettingsPreferredDay = $serviceSettingsPreferredDay;
        $this->serviceSettingsPreferredTime = $serviceSettingsPreferredTime;
        $this->serviceSettingsPreferredLocation = $serviceSettingsPreferredLocation;
        $this->serviceSettingsPreferredNeighbour = $serviceSettingsPreferredNeighbour;
        $this->serviceSettingsParcelAnnouncement = $serviceSettingsParcelAnnouncement;
        $this->serviceSettingsVisualCheckOfAge = $serviceSettingsVisualCheckOfAge;
        $this->serviceSettingsReturnShipment = $serviceSettingsReturnShipment;
        $this->serviceSettingsInsurance = $serviceSettingsInsurance;
        $this->serviceSettingsBulkyGoods = $serviceSettingsBulkyGoods;
        $this->serviceSettingsParcelOutletRouting = $serviceSettingsParcelOutletRouting;
        $this->serviceSettingsCod = $serviceSettingsCod;
        $this->serviceSettingsPrintOnlyIfCodeable = $serviceSettingsPrintOnlyIfCodeable;
        $this->sequenceNumber = $sequenceNumber;
        $this->labelResponseType = $labelResponseType;
    }

    /**
     * @return string
     */
    public function getShipperAccountUser()
    {
        return $this->shipperAccountUser;
    }

    /**
     * @return string
     */
    public function getShipperAccountSignature()
    {
        return $this->shipperAccountSignature;
    }

    /**
     * @return string
     */
    public function getShipperAccountEkp()
    {
        return $this->shipperAccountEkp;
    }

    /**
     * @return string[]
     */
    public function getShipperAccountParticipations()
    {
        return $this->shipperAccountParticipations;
    }

    /**
     * @param string $procedure
     * @return null|string
     */
    public function getShipperAccountParticipation($procedure)
    {
        if (!isset($this->shipperAccountParticipations[$procedure])) {
            return null;
        }
        return $this->shipperAccountParticipations[$procedure];
    }

    /**
     * @return string
     */
    public function getShipperBankDataAccountOwner()
    {
        return $this->shipperBankDataAccountOwner;
    }

    /**
     * @return string
     */
    public function getShipperBankDataBankName()
    {
        return $this->shipperBankDataBankName;
    }

    /**
     * @return string
     */
    public function getShipperBankDataIban()
    {
        return $this->shipperBankDataIban;
    }

    /**
     * @return string
     */
    public function getShipperBankDataBic()
    {
        return $this->shipperBankDataBic;
    }

    /**
     * @return string
     */
    public function getShipperBankDataNote1()
    {
        return $this->shipperBankDataNote1;
    }

    /**
     * @return string
     */
    public function getShipperBankDataNote2()
    {
        return $this->shipperBankDataNote2;
    }

    /**
     * @return string
     */
    public function getShipperBankDataAccountReference()
    {
        return $this->shipperBankDataAccountReference;
    }

    /**
     * @return mixed
     */
    public function getShipperContactName1()
    {
        return $this->shipperContactName1;
    }

    /**
     * @return string
     */
    public function getShipperContactName2()
    {
        return $this->shipperContactName2;
    }

    /**
     * @return string
     */
    public function getShipperContactName3()
    {
        return $this->shipperContactName3;
    }

    /**
     * @return string
     */
    public function getShipperContactStreetName()
    {
        return $this->shipperContactStreetName;
    }

    /**
     * @return string
     */
    public function getShipperContactStreetNumber()
    {
        return $this->shipperContactStreetNumber;
    }

    /**
     * @return string
     */
    public function getShipperContactAddressAddition()
    {
        return $this->shipperContactAddressAddition;
    }

    /**
     * @return string
     */
    public function getShipperContactDispatchingInformation()
    {
        return $this->shipperContactDispatchingInformation;
    }

    /**
     * @return string
     */
    public function getShipperContactZip()
    {
        return $this->shipperContactZip;
    }

    /**
     * @return string
     */
    public function getShipperContactCity()
    {
        return $this->shipperContactCity;
    }

    /**
     * @return string
     */
    public function getShipperContactCountry()
    {
        return $this->shipperContactCountry;
    }

    /**
     * @return string
     */
    public function getShipperContactCountryISOCode()
    {
        return $this->shipperContactCountryISOCode;
    }

    /**
     * @return string
     */
    public function getShipperContactState()
    {
        return $this->shipperContactState;
    }

    /**
     * @return string
     */
    public function getShipperContactPhone()
    {
        return $this->shipperContactPhone;
    }

    /**
     * @return string
     */
    public function getShipperContactEmail()
    {
        return $this->shipperContactEmail;
    }

    /**
     * @return string
     */
    public function getShipperContactContactPerson()
    {
        return $this->shipperContactContactPerson;
    }

    /**
     * @return mixed
     */
    public function getShipperReturnReceiverName1()
    {
        return $this->shipperReturnReceiverName1;
    }

    /**
     * @return string
     */
    public function getShipperReturnReceiverName2()
    {
        return $this->shipperReturnReceiverName2;
    }

    /**
     * @return string
     */
    public function getShipperReturnReceiverName3()
    {
        return $this->shipperReturnReceiverName3;
    }

    /**
     * @return string
     */
    public function getShipperReturnReceiverStreetName()
    {
        return $this->shipperReturnReceiverStreetName;
    }

    /**
     * @return string
     */
    public function getShipperReturnReceiverStreetNumber()
    {
        return $this->shipperReturnReceiverStreetNumber;
    }

    /**
     * @return string
     */
    public function getShipperReturnReceiverAddressAddition()
    {
        return $this->shipperReturnReceiverAddressAddition;
    }

    /**
     * @return string
     */
    public function getShipperReturnReceiverDispatchingInformation()
    {
        return $this->shipperReturnReceiverDispatchingInformation;
    }

    /**
     * @return string
     */
    public function getShipperReturnReceiverZip()
    {
        return $this->shipperReturnReceiverZip;
    }

    /**
     * @return string
     */
    public function getShipperReturnReceiverCity()
    {
        return $this->shipperReturnReceiverCity;
    }

    /**
     * @return string
     */
    public function getShipperReturnReceiverCountry()
    {
        return $this->shipperReturnReceiverCountry;
    }

    /**
     * @return string
     */
    public function getShipperReturnReceiverCountryISOCode()
    {
        return $this->shipperReturnReceiverCountryISOCode;
    }

    /**
     * @return string
     */
    public function getShipperReturnReceiverState()
    {
        return $this->shipperReturnReceiverState;
    }

    /**
     * @return string
     */
    public function getShipperReturnReceiverPhone()
    {
        return $this->shipperReturnReceiverPhone;
    }

    /**
     * @return string
     */
    public function getShipperReturnReceiverEmail()
    {
        return $this->shipperReturnReceiverEmail;
    }

    /**
     * @return string
     */
    public function getShipperReturnReceiverContactPerson()
    {
        return $this->shipperReturnReceiverContactPerson;
    }

    /**
     * @return mixed
     */
    public function getReceiverName1()
    {
        return $this->receiverName1;
    }

    /**
     * @return string
     */
    public function getReceiverName2()
    {
        return $this->receiverName2;
    }

    /**
     * @return string
     */
    public function getReceiverName3()
    {
        return $this->receiverName3;
    }

    /**
     * @return string
     */
    public function getReceiverStreetName()
    {
        return $this->receiverStreetName;
    }

    /**
     * @return string
     */
    public function getReceiverStreetNumber()
    {
        return $this->receiverStreetNumber;
    }

    /**
     * @return string
     */
    public function getReceiverAddressAddition()
    {
        return $this->receiverAddressAddition;
    }

    /**
     * @return string
     */
    public function getReceiverDispatchingInformation()
    {
        return $this->receiverDispatchingInformation;
    }

    /**
     * @return string
     */
    public function getReceiverZip()
    {
        return $this->receiverZip;
    }

    /**
     * @return string
     */
    public function getReceiverCity()
    {
        return $this->receiverCity;
    }

    /**
     * @return string
     */
    public function getReceiverCountry()
    {
        return $this->receiverCountry;
    }

    /**
     * @return string
     */
    public function getReceiverCountryISOCode()
    {
        return $this->receiverCountryISOCode;
    }

    /**
     * @return string
     */
    public function getReceiverState()
    {
        return $this->receiverState;
    }

    /**
     * @return string
     */
    public function getReceiverPhone()
    {
        return $this->receiverPhone;
    }

    /**
     * @return string
     */
    public function getReceiverEmail()
    {
        return $this->receiverEmail;
    }

    /**
     * @return string
     */
    public function getReceiverContactPerson()
    {
        return $this->receiverContactPerson;
    }

    /**
     * @return string
     */
    public function getPackstationZip()
    {
        return $this->packstationZip;
    }

    /**
     * @return string
     */
    public function getPackstationCity()
    {
        return $this->packstationCity;
    }

    /**
     * @return string
     */
    public function getPackstationPackstationNumber()
    {
        return $this->packstationPackstationNumber;
    }

    /**
     * @return string
     */
    public function getPackstationPostNumber()
    {
        return $this->packstationPostNumber;
    }

    /**
     * @return string
     */
    public function getGlobalSettingsLabelType()
    {
        return $this->globalSettingsLabelType;
    }

    /**
     * @return string
     */
    public function getShipmentSettingsDate()
    {
        return $this->shipmentSettingsDate;
    }

    /**
     * @return string
     */
    public function getShipmentSettingsReference()
    {
        return $this->shipmentSettingsReference;
    }

    /**
     * @return float
     */
    public function getShipmentSettingsWeight()
    {
        return $this->shipmentSettingsWeight;
    }

    /**
     * @return string
     */
    public function getShipmentSettingsProduct()
    {
        return $this->shipmentSettingsProduct;
    }

    /**
     * @return mixed
     */
    public function getServiceSettingsPreferredDay()
    {
        return $this->serviceSettingsPreferredDay;
    }

    /**
     * @return bool|string
     */
    public function getServiceSettingsPreferredTime()
    {
        return $this->serviceSettingsPreferredTime;
    }

    /**
     * @return bool|string
     */
    public function getServiceSettingsPreferredLocation()
    {
        return $this->serviceSettingsPreferredLocation;
    }

    /**
     * @return bool|string
     */
    public function getServiceSettingsPreferredNeighbour()
    {
        return $this->serviceSettingsPreferredNeighbour;
    }

    /**
     * @return int
     */
    public function getServiceSettingsParcelAnnouncement()
    {
        return $this->serviceSettingsParcelAnnouncement;
    }

    /**
     * @return bool|string
     */
    public function getServiceSettingsVisualCheckOfAge()
    {
        return $this->serviceSettingsVisualCheckOfAge;
    }

    /**
     * @return boolean
     */
    public function isServiceSettingsReturnShipment()
    {
        return $this->serviceSettingsReturnShipment;
    }

    /**
     * @return bool|string
     */
    public function getServiceSettingsInsurance()
    {
        return $this->serviceSettingsInsurance;
    }

    /**
     * @return boolean
     */
    public function isServiceSettingsBulkyGoods()
    {
        return $this->serviceSettingsBulkyGoods;
    }

    /**
     * @return bool|string
     */
    public function getServiceSettingsParcelOutletRouting()
    {
        return $this->serviceSettingsParcelOutletRouting;
    }

    /**
     * @return bool|float
     */
    public function getServiceSettingsCod()
    {
        return $this->serviceSettingsCod;
    }

    /**
     * @return boolean
     */
    public function isServiceSettingsPrintOnlyIfCodeable()
    {
        return $this->serviceSettingsPrintOnlyIfCodeable;
    }

    /**
     * @return int
     */
    public function getSequenceNumber()
    {
        return $this->sequenceNumber;
    }

    /**
     * @return string
     */
    public function getLabelResponseType()
    {
        return $this->labelResponseType;
    }
}
