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
namespace Dhl\Versenden\Webservice\RequestData\ShipmentOrder\Settings;
use Dhl\Versenden\Webservice\RequestData;

/**
 * ServiceSettings
 *
 * @category Dhl
 * @package  Dhl\Versenden\Webservice\RequestData
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class ServiceSettings extends RequestData implements \JsonSerializable
{
    /** @var bool|string false or date */
    private $dayOfDelivery;
    /** @var bool|string false or time */
    private $deliveryTimeFrame;
    /** @var bool|string false or location */
    private $preferredLocation;
    /** @var bool|string false or neighbour address */
    private $preferredNeighbour;
    /** @var int yes/no/optional */
    private $parcelAnnouncement;
    /** @var bool|string false or A16 or A18 */
    private $visualCheckOfAge;
    /** @var bool false or true */
    private $returnShipment;
    /** @var bool|string false or A or B */
    private $insurance;
    /** @var bool false or true */
    private $bulkyGoods;

    /**
     * ServiceSettings constructor.
     * @param bool|string $dayOfDelivery
     * @param bool|string $deliveryTimeFrame
     * @param bool|string $preferredLocation
     * @param bool|string $preferredNeighbour
     * @param int $parcelAnnouncement
     * @param bool|string $visualCheckOfAge
     * @param bool $returnShipment
     * @param bool|string $insurance
     * @param bool $bulkyGoods
     */
    public function __construct(
        $dayOfDelivery, $deliveryTimeFrame, $preferredLocation, $preferredNeighbour,
        $parcelAnnouncement, $visualCheckOfAge, $returnShipment, $insurance, $bulkyGoods
    ) {
        $this->dayOfDelivery = $dayOfDelivery;
        $this->deliveryTimeFrame = $deliveryTimeFrame;
        $this->preferredLocation = $preferredLocation;
        $this->preferredNeighbour = $preferredNeighbour;
        $this->parcelAnnouncement = $parcelAnnouncement;
        $this->visualCheckOfAge = $visualCheckOfAge;
        $this->returnShipment = $returnShipment;
        $this->insurance = $insurance;
        $this->bulkyGoods = $bulkyGoods;
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
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
