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
use Dhl\Versenden\Bcs\Api\Webservice\RequestData;

/**
 * Person
 *
 * @category Dhl
 * @package  Dhl\Versenden\Bcs\Api\Webservice\RequestData
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
abstract class Person extends RequestData
{
    /** @var string */
    protected $name1;
    /** @var string */
    protected $name2;
    /** @var string */
    protected $name3;
    /** @var string */
    protected $streetName;
    /** @var string */
    protected $streetNumber;
    /** @var string */
    protected $addressAddition;
    /** @var string */
    protected $dispatchingInformation;
    /** @var string */
    protected $zip;
    /** @var string */
    protected $city;
    /** @var string */
    protected $country;
    /** @var string */
    protected $countryISOCode;
    /** @var string */
    protected $state;

    /** @var string */
    protected $phone;
    /** @var string */
    protected $email;
    /** @var string */
    protected $contactPerson;

    /**
     * Person constructor.
     *
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
     */
    public function __construct(
        $name1, $name2, $name3, $streetName, $streetNumber, $addressAddition,
        $dispatchingInformation, $zip, $city, $country, $countryISOCode, $state,
        $phone, $email, $contactPerson
    ) {
        $this->validateLength('Name', $name1, 1, 50);
        $this->validateLength('Name (2)', $name2, 0, 50);
        $this->validateLength('Name (3)', $name3, 0, 50);
        $this->validateLength('Street Name', $streetName, 1, 35);
        $this->validateLength('Street Number', $streetNumber, 1, 7);
        $this->validateLength('Address Addition', $addressAddition, 0, 35);
        $this->validateLength('Dispatching Information', $dispatchingInformation, 0, 35);
        $this->validateLength('Postal Code', $zip, 1, 10);
        $this->validateLength('City', $city, 1, 35);
        $this->validateLength('Country', $country, 0, 30);
        $this->validateLength('Country ISO Code', $countryISOCode, 2, 2);
        $this->validateLength('Region', $state, 0, 30);
        $this->validateLength('Phone', $phone, 0, 20);
        $this->validateLength('E-Mail', $email, 0, 50);
        $this->validateLength('Contact Person', $contactPerson, 0, 50);

        $this->name1 = $name1;
        $this->name2 = $name2;
        $this->name3 = $name3;
        $this->streetName = $streetName;
        $this->streetNumber = $streetNumber;
        $this->addressAddition = $addressAddition;
        $this->dispatchingInformation = $dispatchingInformation;
        $this->zip = $zip;
        $this->city = $city;
        $this->country = $country;
        $this->countryISOCode = $countryISOCode;
        $this->state = $state;
        $this->phone = $phone;
        $this->email = $email;
        $this->contactPerson = $contactPerson;
    }

    /**
     * @return string
     */
    public function getName1()
    {
        return $this->name1;
    }

    /**
     * @return string
     */
    public function getName2()
    {
        return $this->name2;
    }

    /**
     * @return string
     */
    public function getName3()
    {
        return $this->name3;
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
     * @return string
     */
    public function getAddressAddition()
    {
        return $this->addressAddition;
    }

    /**
     * @return string
     */
    public function getDispatchingInformation()
    {
        return $this->dispatchingInformation;
    }

    /**
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @return string
     */
    public function getCountryISOCode()
    {
        return $this->countryISOCode;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getContactPerson()
    {
        return $this->contactPerson;
    }
}
