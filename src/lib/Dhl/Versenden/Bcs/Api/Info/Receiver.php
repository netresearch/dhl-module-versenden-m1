<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Info;

class Receiver extends ArrayableInfo
{
    /** @var Receiver\Packstation */
    public $packstation;
    /** @var Receiver\Postfiliale */
    public $postfiliale;
    /** @var Receiver\ParcelShop */
    public $parcelShop;

    /** @var string */
    public $name1;
    /** @var string */
    public $name2;
    /** @var string */
    public $name3;
    /** @var string */
    public $streetName;
    /** @var string */
    public $streetNumber;
    /** @var string */
    public $addressAddition;
    /** @var string */
    public $dispatchingInformation;
    /** @var string */
    public $zip;
    /** @var string */
    public $city;
    /** @var string */
    public $country;
    /** @var string */
    public $countryISOCode;
    /** @var string */
    public $state;

    /** @var string */
    public $phone;
    /** @var string */
    public $email;
    /** @var string */
    public $contactPerson;

    /**
     * Receiver constructor.
     */
    public function __construct()
    {
        $this->packstation = new Receiver\Packstation();
        $this->postfiliale = new Receiver\Postfiliale();
        $this->parcelShop  = new Receiver\ParcelShop();
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
