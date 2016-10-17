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
namespace Dhl\Versenden\Webservice\RequestData\ShipmentOrder;
use Dhl\Versenden\Webservice\RequestData;

/**
 * ServiceSelection
 *
 * @category Dhl
 * @package  Dhl\Versenden\Webservice\RequestData
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class ServiceSelection extends RequestData
{
    /** @var bool|string false or date */
    private $dayOfDelivery;
    /** @var bool|string false or time */
    private $deliveryTimeFrame;
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
    /** @var bool|float false or amount */
    private $insurance;
    /** @var bool false or true */
    private $bulkyGoods;
    /** @var bool|float false or amount */
    private $cod;
    /** @var bool false or true */
    private $printOnlyIfCodeable;

    /**
     * ServiceSelection constructor.
     * @param bool|string $dayOfDelivery
     * @param bool|string $deliveryTimeFrame
     * @param bool|string $preferredLocation
     * @param bool|string $preferredNeighbour
     * @param bool $parcelAnnouncement
     * @param bool|string $visualCheckOfAge
     * @param bool $returnShipment
     * @param bool|float $insurance
     * @param bool $bulkyGoods
     * @param bool|float $cod
     * @param bool $printOnlyIfCodeable
     */
    public function __construct(
        $dayOfDelivery, $deliveryTimeFrame, $preferredLocation, $preferredNeighbour,
        $parcelAnnouncement, $visualCheckOfAge, $returnShipment, $insurance,
        $bulkyGoods, $cod, $printOnlyIfCodeable)
    {
        $this->dayOfDelivery = $dayOfDelivery;
        $this->deliveryTimeFrame = $deliveryTimeFrame;
        $this->preferredLocation = $preferredLocation;
        $this->preferredNeighbour = $preferredNeighbour;
        $this->parcelAnnouncement = $parcelAnnouncement;
        $this->visualCheckOfAge = $visualCheckOfAge;
        $this->returnShipment = $returnShipment;
        $this->insurance = $insurance;
        $this->bulkyGoods = $bulkyGoods;
        $this->cod = $cod;
        $this->printOnlyIfCodeable = $printOnlyIfCodeable;
    }

    /**
     * @return bool|string
     */
    public function getDayOfDelivery()
    {
        return $this->dayOfDelivery;
    }

    /**
     * @return bool|string
     */
    public function getDeliveryTimeFrame()
    {
        return $this->deliveryTimeFrame;
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
     * @return bool|float
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