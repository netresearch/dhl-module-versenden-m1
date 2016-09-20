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
 * @package   Dhl\Versenden\Webservice\RequestData
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
namespace Dhl\Versenden\Webservice\RequestData;
use Dhl\Versenden\Webservice\RequestData\ShipmentOrder\Package;
use Dhl\Versenden\Webservice\RequestData\ShipmentOrder\PackageCollection;
use Dhl\Versenden\Webservice\RequestData\ShipmentOrder\Receiver;
use Dhl\Versenden\Webservice\RequestData\ShipmentOrder\ServiceSelection;

/**
 * ObjectMapper
 *
 * @category Dhl
 * @package  Dhl\Versenden\Webservice\RequestData
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class ObjectMapper
{
    const SCHEMA_VERSION = '1.0';

    /**
     * @param \stdClass $object
     * @return ServiceSelection
     */
    protected static function getServiceSelection(\stdClass $object)
    {
        $dayOfDelivery = isset($object->dayOfDelivery) ? $object->dayOfDelivery : false;
        $deliveryTimeFrame = isset($object->deliveryTimeFrame) ? $object->deliveryTimeFrame : false;
        $preferredLocation = isset($object->preferredLocation) ? $object->preferredLocation : false;
        $preferredNeighbour = isset($object->preferredNeighbour) ? $object->preferredNeighbour : false;
        $parcelAnnouncement = isset($object->parcelAnnouncement) ? $object->parcelAnnouncement : 0;
        $visualCheckOfAge = isset($object->visualCheckOfAge) ? $object->visualCheckOfAge : false;
        $returnShipment = isset($object->returnShipment) ? $object->returnShipment : false;
        $insurance = isset($object->insurance) ? $object->insurance : false;
        $bulkyGoods = isset($object->bulkyGoods) ? $object->bulkyGoods : false;
        $cod = isset($object->cod) ? $object->cod : false;
        $printOnlyIfCodeable = isset($object->printOnlyIfCodeable) ? $object->printOnlyIfCodeable : false;

        return ServiceSelection::fromProperties(
            $dayOfDelivery,
            $deliveryTimeFrame,
            $preferredLocation,
            $preferredNeighbour,
            $parcelAnnouncement,
            $visualCheckOfAge,
            $returnShipment,
            $insurance,
            $bulkyGoods,
            $cod,
            $printOnlyIfCodeable
        );
    }

    /**
     * @param \stdClass $packagesObject
     * @return PackageCollection
     */
    protected static function getPackages(\stdClass  $packagesObject)
    {
        $packageCollection = new PackageCollection();
        $packages = $packagesObject->packages;

        $i = 0;
        while (isset($packages->{$i})) {
            $object = $packages->{$i};
            $sequenceNumber = isset($object->sequenceNumber) ? $object->sequenceNumber : 0;
            $weightInKG = isset($object->weightInKG) ? $object->weightInKG : 0;
            $lengthInCM = isset($object->lengthInCM) ? $object->lengthInCM : null;
            $widthInCM = isset($object->widthInCM) ? $object->widthInCM : null;
            $heightInCM = isset($object->heightInCM) ? $object->heightInCM : null;

            $package = new Package(
                $sequenceNumber,
                $weightInKG,
                $lengthInCM,
                $widthInCM,
                $heightInCM
            );
            $packageCollection->addItem($package);

            $i++;
        }

        return $packageCollection;
    }

    /**
     * @param \stdClass $object
     * @param string $zip Receiver Postal Code
     * @param string $city Receiver City
     * @param string $country Receiver Country
     * @param string $countryISOCode Receiver Country ISO Code
     * @param string $state Receiver Region
     * @return Receiver\Packstation
     */
    protected static function getPackstation(\stdClass $object, $zip, $city, $country, $countryISOCode, $state)
    {
        $zip = isset($object->zip) ? $object->zip : $zip;
        $city = isset($object->city) ? $object->city : $city;
        $country = isset($object->country) ? $object->country : $country;
        $countryISOCode = isset($object->countryISOCode) ? $object->countryISOCode : $countryISOCode;
        $state = isset($object->state) ? $object->state : $state;

        $packstationNumber = isset($object->packstationNumber) ? $object->packstationNumber : '';
        $postNumber = isset($object->postNumber) ? $object->postNumber : '';

        return new Receiver\Packstation(
            $zip,
            $city,
            $country,
            $countryISOCode,
            $state,
            $packstationNumber,
            $postNumber
        );
    }

    /**
     * @param \stdClass $object
     * @param string $zip Receiver Postal Code
     * @param string $city Receiver City
     * @param string $country Receiver Country
     * @param string $countryISOCode Receiver Country ISO Code
     * @param string $state Receiver Region
     * @return Receiver\ParcelShop
     */
    protected static function getParcelShop(\stdClass $object, $zip, $city, $country, $countryISOCode, $state)
    {
        $zip = isset($object->zip) ? $object->zip : $zip;
        $city = isset($object->city) ? $object->city : $city;
        $country = isset($object->country) ? $object->country : $country;
        $countryISOCode = isset($object->countryISOCode) ? $object->countryISOCode : $countryISOCode;
        $state = isset($object->state) ? $object->state : $state;

        $parcelShopNumber = isset($object->parcelShopNumber) ? $object->parcelShopNumber : '';
        $streetName = isset($object->streetName) ? $object->streetName : '';
        $streetNumber = isset($object->streetNumber) ? $object->streetNumber : '';

        return new Receiver\ParcelShop(
            $zip,
            $city,
            $country,
            $countryISOCode,
            $state,
            $parcelShopNumber,
            $streetName,
            $streetNumber
        );
    }

    /**
     * @param \stdClass $object
     * @param string $zip Receiver Postal Code
     * @param string $city Receiver City
     * @param string $country Receiver Country
     * @param string $countryISOCode Receiver Country ISO Code
     * @param string $state Receiver Region
     * @return Receiver\Postfiliale
     */
    protected static function getPostfiliale(\stdClass $object, $zip, $city, $country, $countryISOCode, $state)
    {
        $zip = isset($object->zip) ? $object->zip : $zip;
        $city = isset($object->city) ? $object->city : $city;
        $country = isset($object->country) ? $object->country : $country;
        $countryISOCode = isset($object->countryISOCode) ? $object->countryISOCode : $countryISOCode;
        $state = isset($object->state) ? $object->state : $state;

        $postfilialNumber = isset($object->postfilialNumber) ? $object->postfilialNumber : '';
        $postNumber = isset($object->postNumber) ? $object->postNumber : '';

        return new Receiver\Postfiliale(
            $zip,
            $city,
            $country,
            $countryISOCode,
            $state,
            $postfilialNumber,
            $postNumber
        );
    }

    /**
     * @param \stdClass $object
     * @return Receiver
     */
    protected static function getReceiver(\stdClass $object)
    {
        $name1 = isset($object->name1) ? $object->name1 : '';
        $name2 = isset($object->name2) ? $object->name2 : '';
        $name3 = isset($object->name3) ? $object->name3 : '';
        $streetName = isset($object->streetName) ? $object->streetName : '';
        $streetNumber = isset($object->streetNumber) ? $object->streetNumber : '';
        $addressAddition = isset($object->addressAddition) ? $object->addressAddition : '';
        $dispatchingInformation = isset($object->dispatchingInformation)
            ? $object->dispatchingInformation
            : '';
        $zip = isset($object->zip) ? $object->zip : '';
        $city = isset($object->city) ? $object->city : '';
        $country = isset($object->country) ? $object->country : '';
        $countryISOCode = isset($object->countryISOCode) ? $object->countryISOCode : '';
        $state = isset($object->state) ? $object->state : '';
        $phone = isset($object->phone) ? $object->phone : '';
        $email = isset($object->email) ? $object->email : '';
        $contactPerson = isset($object->contactPerson) ? $object->contactPerson : '';

        $packstation = isset($object->packstation)
            ? static::getPackstation($object->packstation, $zip, $city, $country, $countryISOCode, $state)
            : null;
        $postfiliale = isset($object->postfiliale)
            ? static::getPostfiliale($object->postfiliale, $zip, $city, $country, $countryISOCode, $state)
            : null;
        $parcelShop = isset($object->parcelShop)
            ? static::getParcelShop($object->parcelShop, $zip, $city, $country, $countryISOCode, $state)
            : null;

        return new Receiver(
            $name1,
            $name2,
            $name3,
            $streetName,
            $streetNumber,
            $addressAddition,
            $dispatchingInformation,
            $zip,
            $city,
            $country,
            $countryISOCode,
            $state,
            $phone,
            $email,
            $contactPerson,
            $packstation,
            $postfiliale,
            $parcelShop
        );
    }

    /**
     * @param \stdClass $object
     * @return ShippingInfo|null
     */
    public static function getShippingInfo(\stdClass $object)
    {
        if (!self::canMap($object)) {
            return null;
        }

        $receiver = isset($object->receiver)
            ? static::getReceiver($object->receiver)
            : null;
        $serviceSettings = isset($object->serviceSelection)
            ? static::getServiceSelection($object->serviceSelection)
            : null;
        $packages = isset($object->packages)
            ? static::getPackages($object->packages)
            : null;

        return new ShippingInfo($receiver, $serviceSettings, $packages);
    }

    /**
     * Check if the given stdClass can be mapped to RequestData objects
     * @param \stdClass $object
     * @return bool
     */
     protected static function canMap(\stdClass $object)
     {
         return (isset($object->schemaVersion) && $object->schemaVersion == self::SCHEMA_VERSION);
     }
}
