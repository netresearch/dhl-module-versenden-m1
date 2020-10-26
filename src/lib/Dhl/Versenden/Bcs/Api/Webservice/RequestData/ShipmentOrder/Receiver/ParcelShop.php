<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder\Receiver;

class ParcelShop extends PostalFacility
{
    /** @var string */
    private $parcelShopNumber;
    /** @var string */
    private $streetName;
    /** @var string */
    private $streetNumber;

    /**
     * ParcelShop constructor.
     * @param string $zip
     * @param string $city
     * @param string $country
     * @param string $countryISOCode
     * @param string $state
     * @param string $parcelShopNumber
     * @param string $streetName
     * @param string $streetNumber
     */
    public function __construct($zip, $city, $country, $countryISOCode, $state,
                                $parcelShopNumber, $streetName, $streetNumber)
    {
        $this->parcelShopNumber = $parcelShopNumber;
        $this->streetName = $streetName;
        $this->streetNumber = $streetNumber;

        parent::__construct($zip, $city, $country, $countryISOCode, $state);
    }

    /**
     * @return string
     */
    public function getParcelShopNumber()
    {
        return $this->parcelShopNumber;
    }

    /**
     * @return string
     */
    public function getStreetName()
    {
        return $this->streetName;
    }

    /**
     * @return string
     */
    public function getStreetNumber()
    {
        return $this->streetNumber;
    }
}
