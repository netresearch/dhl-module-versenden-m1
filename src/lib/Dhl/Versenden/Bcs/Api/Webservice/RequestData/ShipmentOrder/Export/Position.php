<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder\Export;

use Dhl\Versenden\Bcs\Api\Webservice\RequestData;

class Position extends RequestData
{
    /** @var int */
    private $sequenceNumber;
    /** @var string */
    private $description;
    /** @var string */
    private $countryCodeOrigin;
    /** @var string */
    private $tariffNumber;
    /** @var float */
    private $amount;
    /** @var float */
    private $netWeightInKG;
    /** @var float */
    private $value;

    /**
     * Position constructor.
     * @param int $sequenceNumber
     * @param string $description
     * @param string $countryCodeOrigin
     * @param string $tariffNumber
     * @param float $amount
     * @param float $netWeightInKG
     * @param float $value
     */
    public function __construct($sequenceNumber, $description, $countryCodeOrigin,
                                $tariffNumber, $amount, $netWeightInKG, $value)
    {
        $this->sequenceNumber = $sequenceNumber;
        $this->description = $description;
        $this->countryCodeOrigin = $countryCodeOrigin;
        $this->tariffNumber = $tariffNumber;
        $this->amount = $amount;
        $this->netWeightInKG = $netWeightInKG;
        $this->value = $value;
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
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getCountryCodeOrigin()
    {
        return $this->countryCodeOrigin;
    }

    /**
     * @return string
     */
    public function getTariffNumber()
    {
        return $this->tariffNumber;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return float
     */
    public function getNetWeightInKG()
    {
        return $this->netWeightInKG;
    }

    /**
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }
}
