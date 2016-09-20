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
class ServiceSelection extends RequestData implements \JsonSerializable
{
    /** @var bool|string false or date */
    private $dayOfDelivery = false;
    /** @var bool|string false or time */
    private $deliveryTimeFrame = false;
    /** @var bool|string false or location */
    private $preferredLocation = false;
    /** @var bool|string false or neighbour address */
    private $preferredNeighbour = false;
    /** @var bool false or true */
    private $parcelAnnouncement = false;
    /** @var bool|string false or A16 or A18 */
    private $visualCheckOfAge = false;
    /** @var bool false or true */
    private $returnShipment = false;
    /** @var bool|float false or amount */
    private $insurance = false;
    /** @var bool false or true */
    private $bulkyGoods = false;
    /** @var bool|float false or amount */
    private $cod = false;
    /** @var bool false or true */
    private $printOnlyIfCodeable = false;

    /**
     * Constructs ServiceSettings object from array with values that differ from initial settings
     *
     *
     * @param array $options service setting options that differ from default
     *
     * @return ServiceSelection
     */
    public static function fromArray(array $options)
    {
        $instance = new self();
        array_walk(
            $options,
            function (&$value, $key, &$instance) {
                if (property_exists($instance, $key)) {
                    $instance->$key = $value;
                }
            },
            $instance
        );

        return $instance;
    }


    /**
     * Constructs service setting object from giving each property explicitly
     *
     * @param bool|string $dayOfDelivery
     * @param bool|string $deliveryTimeFrame
     * @param bool|string $preferredLocation
     * @param bool|string $preferredNeighbour
     * @param int         $parcelAnnouncement
     * @param bool|string $visualCheckOfAge
     * @param bool        $returnShipment
     * @param bool|float  $insurance
     * @param bool        $bulkyGoods
     * @param bool|float  $cod
     * @param bool        $printOnlyIfCodeable
     *
     * @return ServiceSelection
     */

    public static function fromProperties(
        $dayOfDelivery, $deliveryTimeFrame, $preferredLocation, $preferredNeighbour,
        $parcelAnnouncement, $visualCheckOfAge, $returnShipment, $insurance,
        $bulkyGoods, $cod, $printOnlyIfCodeable
    ) {
        $instance = new self();
        $instance->dayOfDelivery = $dayOfDelivery;
        $instance->deliveryTimeFrame = $deliveryTimeFrame;
        $instance->preferredLocation = $preferredLocation;
        $instance->preferredNeighbour = $preferredNeighbour;
        $instance->parcelAnnouncement = $parcelAnnouncement;
        $instance->visualCheckOfAge = $visualCheckOfAge;
        $instance->returnShipment = $returnShipment;
        $instance->insurance = $insurance;
        $instance->bulkyGoods = $bulkyGoods;
        $instance->cod = $cod;
        $instance->printOnlyIfCodeable = $printOnlyIfCodeable;

        return $instance;
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

    /**
     * @param string $serviceCode
     * @return mixed
     */
    public function getServiceValue($serviceCode)
    {
        if (property_exists(static::class, $serviceCode)) {
            return $this->{$serviceCode};
        }

        return null;
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *        which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
