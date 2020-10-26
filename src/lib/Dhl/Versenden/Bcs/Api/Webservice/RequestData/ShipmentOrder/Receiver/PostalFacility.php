<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder\Receiver;

use Dhl\Versenden\Bcs\Api\Webservice\RequestData;

abstract class PostalFacility extends RequestData
{
    /** @var string */
    private $zip;
    /** @var string */
    private $city;
    /** @var string */
    private $country;
    /** @var string */
    private $countryISOCode;
    /** @var string */
    private $state;

    /**
     * PostalFacility constructor.
     * @param string $zip
     * @param string $city
     * @param string $country
     * @param string $countryISOCode
     * @param string $state
     */
    public function __construct($zip, $city, $country, $countryISOCode, $state)
    {
        $this->zip = $zip;
        $this->city = $city;
        $this->country = $country;
        $this->countryISOCode = $countryISOCode;
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @return string
     */
    public function getCountryISOCode()
    {
        return $this->countryISOCode;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }
}
