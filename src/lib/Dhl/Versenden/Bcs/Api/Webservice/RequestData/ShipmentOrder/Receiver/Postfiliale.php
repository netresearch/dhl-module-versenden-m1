<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder\Receiver;

class Postfiliale extends PostalFacility
{
    /** @var string */
    private $postfilialNumber;
    /** @var string */
    private $postNumber;

    /**
     * Postfiliale constructor.
     * @param string $zip
     * @param string $city
     * @param string $country
     * @param string $countryISOCode
     * @param string $state
     * @param string $postfilialNumber
     * @param string $postNumber
     */
    public function __construct($zip, $city, $country, $countryISOCode, $state,
                                $postfilialNumber, $postNumber)
    {
        $this->postfilialNumber = $postfilialNumber;
        $this->postNumber = $postNumber;

        parent::__construct($zip, $city, $country, $countryISOCode, $state);
    }

    /**
     * @return string
     */
    public function getPostfilialNumber()
    {
        return $this->postfilialNumber;
    }

    /**
     * @return string
     */
    public function getPostNumber()
    {
        return $this->postNumber;
    }
}
