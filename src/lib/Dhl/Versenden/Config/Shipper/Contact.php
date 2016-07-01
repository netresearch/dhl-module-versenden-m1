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
 * @package   Dhl\Versenden\Config\Shipper
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
namespace Dhl\Versenden\Config\Shipper;
use Dhl\Versenden\Config as ConfigReader;
use Dhl\Versenden\Config\Data as ConfigData;
use Dhl\Versenden\Config\Exception as ConfigException;
/**
 * Contact
 *
 * @category Dhl
 * @package  Dhl\Versenden\Config\Shipper
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Contact extends ConfigData
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

    /**
     * Shift data from config array to properties.
     *
     * @param ConfigReader $reader
     */
    public function loadValues(ConfigReader $reader)
    {
        $this->name1                  = $reader->getValue('contact_name1');
        $this->name2                  = $reader->getValue('contact_name2');
        $this->name3                  = $reader->getValue('contact_name3');
        $this->streetName             = $reader->getValue('contact_streetname');
        $this->streetNumber           = $reader->getValue('contact_streetnumber');
        $this->addressAddition        = $reader->getValue('contact_addition');
        $this->dispatchingInformation = $reader->getValue('contact_dispatchinfo');
        $this->zip                    = $reader->getValue('contact_zip');
        $this->city                   = $reader->getValue('contact_city');
        $this->country                = $reader->getValue('contact_country');
        $this->countryISOCode         = $reader->getValue('contact_countrycode');
        $this->state                  = $reader->getValue('contact_region');

        $this->phone         = $reader->getValue('contact_phone');
        $this->email         = $reader->getValue('contact_email');
        $this->contactPerson = $reader->getValue('contact_person');
    }

    /**
     * Validate values in addition to system.xml frontend validation
     *
     * @param ConfigReader $reader
     * @throws ConfigException
     */
    public function validateValues(ConfigReader $reader)
    {
        $reader->validateLength('Shipper Name', $this->name1, 1, 50);
        $reader->validateLength('Shipper Name (2)', $this->name2, 0, 50);
        $reader->validateLength('Shipper Name (3)', $this->name3, 0, 50);
        $reader->validateLength('Street Name', $this->streetName, 1, 35);
        $reader->validateLength('Street Number', $this->streetNumber, 1, 5);
        $reader->validateLength('Address Addition', $this->addressAddition, 0, 35);
        $reader->validateLength('Dispatching Information', $this->dispatchingInformation, 0, 35);
        $reader->validateLength('Postal Code', $this->zip, 1, 10);
        $reader->validateLength('City', $this->city, 1, 35);
        $reader->validateLength('Country', $this->country, 0, 30);
        $reader->validateLength('Country ISO Code', $this->countryISOCode, 2, 2);
        $reader->validateLength('Region', $this->state, 0, 30);
        $reader->validateLength('Phone', $this->phone, 0, 20);
        $reader->validateLength('E-Mail', $this->email, 0, 50);
        $reader->validateLength('Contact Person', $this->contactPerson, 0, 50);
    }
}
