<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder;

use Dhl\Versenden\Bcs\Api\Webservice\RequestData;

class ServiceSelection extends RequestData
{
    /** @var bool|string false or date */
    private $preferredDay;
    /** @var bool|string false or time */
    private $preferredTime;
    /** @var bool|string false or location */
    private $preferredLocation;
    /** @var bool|string false or neighbour address */
    private $preferredNeighbour;
    /** @var bool false or true */
    private $parcelAnnouncement;
    /** @var bool|string false or A16 or A18 */
    private $visualCheckOfAge;
    /** @var bool false or true */
    private $returnShipment;
    /** @var bool|string false or amount */
    private $insurance;
    /** @var bool false or true */
    private $bulkyGoods;
    /** @var bool|string false or customer email */
    private $parcelOutletRouting;
    /** @var bool|float false or amount */
    private $cod;
    /** @var bool false or true */
    private $printOnlyIfCodeable;

    /**
     * ServiceSelection constructor.
     * @param bool|string $preferredDay
     * @param bool|string $preferredTime
     * @param bool|string $preferredLocation
     * @param bool|string $preferredNeighbour
     * @param bool $parcelAnnouncement
     * @param bool|string $visualCheckOfAge
     * @param bool $returnShipment
     * @param bool|string $insurance
     * @param bool $bulkyGoods
     * @param bool|string $parcelOutletRouting
     * @param bool|float $cod
     * @param bool $printOnlyIfCodeable
     */
    public function __construct(
        $preferredDay,
        $preferredTime,
        $preferredLocation,
        $preferredNeighbour,
        $parcelAnnouncement,
        $visualCheckOfAge,
        $returnShipment,
        $insurance,
        $bulkyGoods,
        $parcelOutletRouting,
        $cod,
        $printOnlyIfCodeable
    ) {
        $this->preferredDay = $preferredDay;
        $this->preferredTime = $preferredTime;
        $this->preferredLocation = $preferredLocation;
        $this->preferredNeighbour = $preferredNeighbour;
        $this->parcelAnnouncement = $parcelAnnouncement;
        $this->visualCheckOfAge = $visualCheckOfAge;
        $this->returnShipment = $returnShipment;
        $this->insurance = $insurance;
        $this->bulkyGoods = $bulkyGoods;
        $this->parcelOutletRouting = $parcelOutletRouting;
        $this->cod = $cod;
        $this->printOnlyIfCodeable = $printOnlyIfCodeable;
    }

    /**
     * @return bool|string
     */
    public function getPreferredDay()
    {
        return $this->preferredDay;
    }

    /**
     * @return bool|string
     */
    public function getPreferredTime()
    {
        return $this->preferredTime;
    }

    /**
     * @return bool|string
     */
    public function getPreferredLocation()
    {
        return $this->preferredLocation;
    }

    /**
     * @return bool|string
     */
    public function getPreferredNeighbour()
    {
        return $this->preferredNeighbour;
    }

    /**
     * @return int
     */
    public function getParcelAnnouncement()
    {
        return $this->parcelAnnouncement;
    }

    /**
     * @return bool|string
     */
    public function getVisualCheckOfAge()
    {
        return $this->visualCheckOfAge;
    }

    /**
     * @return boolean
     */
    public function isReturnShipment()
    {
        return $this->returnShipment;
    }

    /**
     * @return bool|string
     */
    public function getInsurance()
    {
        return $this->insurance;
    }

    /**
     * @return boolean
     */
    public function isBulkyGoods()
    {
        return $this->bulkyGoods;
    }

    /**
     * @return bool|string
     */
    public function getParcelOutletRouting()
    {
        return $this->parcelOutletRouting;
    }

    /**
     * @return bool|float
     */
    public function getCod()
    {
        return $this->cod;
    }

    /**
     * @return boolean
     */
    public function isPrintOnlyIfCodeable()
    {
        return $this->printOnlyIfCodeable;
    }
}
