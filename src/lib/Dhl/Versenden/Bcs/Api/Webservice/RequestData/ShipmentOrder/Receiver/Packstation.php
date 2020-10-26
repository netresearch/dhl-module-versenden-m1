<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder\Receiver;

class Packstation extends PostalFacility
{
    /** @var string */
    private $packstationNumber;
    /** @var string */
    private $postNumber;

    /**
     * Packstation constructor.
     * @param string $zip
     * @param string $city
     * @param string $country
     * @param string $countryISOCode
     * @param string $state
     * @param string $packstationNumber
     * @param string $postNumber
     */
    public function __construct($zip, $city, $country, $countryISOCode, $state,
                                $packstationNumber, $postNumber)
    {
        $this->packstationNumber = $packstationNumber;
        $this->postNumber = $postNumber;

        parent::__construct($zip, $city, $country, $countryISOCode, $state);
    }

    /**
     * @return string
     */
    public function getPackstationNumber()
    {
        return $this->packstationNumber;
    }

    /**
     * @return string
     */
    public function getPostNumber()
    {
        return $this->postNumber;
    }
}
