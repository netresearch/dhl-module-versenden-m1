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
 * @package   Dhl\Versenden\Service
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
namespace Dhl\Versenden\ShippingInfo;
/**
 * Receiver
 *
 * @category Dhl
 * @package  Dhl\Versenden\ShippingInfo
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Receiver
{
    /** @var string */
    public $name1;
    /** @var string */
    public $name2;
    /** @var string */
    public $name3;

    /** @var string */
    public $streetName;
    /** @var string */
    public $streetNumber;
    /** @var string */
    public $addressAddition;
    /** @var string */
    public $dispatchingInformation;
    /** @var string */
    public $zip;
    /** @var string */
    public $city;
    /** @var string */
    public $country;
    /** @var string */
    public $countryISOCode;
    /** @var string */
    public $state;

    /** @var string */
    public $phone;
    /** @var string */
    public $email;
    /** @var string */
    public $contactPerson;

    /** @var Packstation */
    public $packstation;
    /** @var Postfiliale */
    public $postfiliale;
    /** @var ParcelShop */
    public $parcelShop;

    public function __construct(\stdClass $object = null)
    {
        if ($object) {
            $this->name1 = isset($object->name1) ? $object->name1 : '';
            $this->name2 = isset($object->name2) ? $object->name2 : '';
            $this->name3 = isset($object->name3) ? $object->name3 : '';
            $this->streetName = isset($object->streetName) ? $object->streetName : '';
            $this->streetNumber = isset($object->streetNumber) ? $object->streetNumber : '';
            $this->addressAddition = isset($object->addressAddition) ? $object->addressAddition : '';
            $this->dispatchingInformation = isset($object->dispatchingInformation)
                ? $object->dispatchingInformation
                : '';
            $this->zip = isset($object->zip) ? $object->zip : '';
            $this->city = isset($object->city) ? $object->city : '';
            $this->country = isset($object->country) ? $object->country : '';
            $this->countryISOCode = isset($object->countryISOCode) ? $object->countryISOCode : '';
            $this->state = isset($object->state) ? $object->state : '';
            $this->phone = isset($object->phone) ? $object->phone : '';
            $this->email = isset($object->email) ? $object->email : '';
            $this->contactPerson = isset($object->contactPerson) ? $object->contactPerson : '';

            $this->packstation = isset($object->packstation) ? new Packstation($object->packstation) : null;
            $this->postfiliale = isset($object->postfiliale) ? new Postfiliale($object->postfiliale) : null;
            $this->parcelShop = isset($object->parcelShop) ? new ParcelShop($object->parcelShop) : null;
        }
    }
}
