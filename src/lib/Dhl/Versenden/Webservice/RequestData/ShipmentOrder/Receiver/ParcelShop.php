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
namespace Dhl\Versenden\Webservice\RequestData\ShipmentOrder\Receiver;
/**
 * ParcelShop
 *
 * @category Dhl
 * @package  Dhl\Versenden\Webservice\RequestData
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class ParcelShop extends PostalFacility implements \JsonSerializable
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
     * @param string $parcelShopNumber
     * @param string $streetName
     * @param string $streetNumber
     */
    public function __construct(
        $zip, $city, $parcelShopNumber, $streetName, $streetNumber
    ) {
        $this->parcelShopNumber = $parcelShopNumber;
        $this->streetName = $streetName;
        $this->streetNumber = $streetNumber;

        parent::__construct($zip, $city);
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
