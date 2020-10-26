<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder;

class Receiver extends Person
{
    /** @var Receiver\Packstation */
    private $packstation;
    /** @var Receiver\Postfiliale */
    private $postfiliale;
    /** @var Receiver\ParcelShop */
    private $parcelShop;


    /**
     * Receiver constructor.
     * @param string $name1
     * @param string $name2
     * @param string $name3
     * @param string $streetName
     * @param string $streetNumber
     * @param string $addressAddition
     * @param string $dispatchingInformation
     * @param string $zip
     * @param string $city
     * @param string $country
     * @param string $countryISOCode
     * @param string $state
     * @param string $phone
     * @param string $email
     * @param string $contactPerson
     * @param Receiver\Packstation|null $packStation
     * @param Receiver\Postfiliale|null $postFiliale
     * @param Receiver\ParcelShop|null $parcelShop
     */
    public function __construct(
        $name1, $name2, $name3, $streetName, $streetNumber, $addressAddition, $dispatchingInformation,
        $zip, $city, $country, $countryISOCode, $state, $phone, $email, $contactPerson,
        Receiver\Packstation $packStation = null,
        Receiver\Postfiliale $postFiliale = null,
        Receiver\ParcelShop $parcelShop = null
    ) {
        $this->packstation = $packStation;
        $this->postfiliale = $postFiliale;
        $this->parcelShop  = $parcelShop;

        parent::__construct(
            $name1, $name2, $name3, $streetName, $streetNumber,
            $addressAddition, $dispatchingInformation, $zip, $city, $country,
            $countryISOCode, $state, $phone, $email, $contactPerson
        );
    }

    /**
     * @return Receiver\Packstation
     */
    public function getPackstation()
    {
        return $this->packstation;
    }

    /**
     * @return Receiver\Postfiliale
     */
    public function getPostfiliale()
    {
        return $this->postfiliale;
    }

    /**
     * @return Receiver\ParcelShop
     */
    public function getParcelShop()
    {
        return $this->parcelShop;
    }
}
