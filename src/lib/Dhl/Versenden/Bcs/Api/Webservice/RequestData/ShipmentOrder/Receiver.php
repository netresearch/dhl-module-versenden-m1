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
 * @package   Dhl\Versenden\Bcs\Api\Webservice\RequestData
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
namespace Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder;

/**
 * Receiver
 *
 * @category Dhl
 * @package  Dhl\Versenden\Bcs\Api\Webservice\RequestData
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Receiver extends Person
{
    /** @var Receiver\Packstation */
    private $packstation;
    /** @var Receiver\Postfiliale */
    private $postfiliale;
    /** @var Receiver\ParcelShop */
    private $parcelShop;


    /**
     * Receiver constructor.
     * @param string $name1
     * @param string $name2
     * @param string $name3
     * @param string $streetName
     * @param string $streetNumber
     * @param string $addressAddition
     * @param string $dispatchingInformation
     * @param string $zip
     * @param string $city
     * @param string $country
     * @param string $countryISOCode
     * @param string $state
     * @param string $phone
     * @param string $email
     * @param string $contactPerson
     * @param Receiver\Packstation|null $packStation
     * @param Receiver\Postfiliale|null $postFiliale
     * @param Receiver\ParcelShop|null $parcelShop
     */
    public function __construct(
        $name1, $name2, $name3, $streetName, $streetNumber, $addressAddition, $dispatchingInformation,
        $zip, $city, $country, $countryISOCode, $state, $phone, $email, $contactPerson,
        Receiver\Packstation $packStation = null,
        Receiver\Postfiliale $postFiliale = null,
        Receiver\ParcelShop $parcelShop = null
    ) {
        $this->packstation = $packStation;
        $this->postfiliale = $postFiliale;
        $this->parcelShop  = $parcelShop;

        parent::__construct(
            $name1, $name2, $name3, $streetName, $streetNumber,
            $addressAddition, $dispatchingInformation, $zip, $city, $country,
            $countryISOCode, $state, $phone, $email, $contactPerson
        );
    }

    /**
     * @return Receiver\Packstation
     */
    public function getPackstation()
    {
        return $this->packstation;
    }

    /**
     * @return Receiver\Postfiliale
     */
    public function getPostfiliale()
    {
        return $this->postfiliale;
    }

    /**
     * @return Receiver\ParcelShop
     */
    public function getParcelShop()
    {
        return $this->parcelShop;
    }
}
