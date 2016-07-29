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
 * @package   Dhl\Versenden
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
namespace Dhl\Versenden\Config\Shipper;
use Dhl\Versenden\Config as ConfigReader;
/**
 * ReturnReceiver
 *
 * @deprecated
 * @see \Dhl\Versenden\Webservice\RequestData\ShipmentOrder\Shipper\ReturnReceiver
 * @category Dhl
 * @package  Dhl\Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class ReturnReceiver extends Contact
{
    /**
     * Shift data from config array to properties.
     *
     * @param ConfigReader $reader
     */
    public function loadValues(ConfigReader $reader)
    {
        $this->name1                  = $reader->getValue('returnshipment_name1');
        $this->name2                  = $reader->getValue('returnshipment_name2');
        $this->name3                  = $reader->getValue('returnshipment_name3');
        $this->streetName             = $reader->getValue('returnshipment_streetname');
        $this->streetNumber           = $reader->getValue('returnshipment_streetnumber');
        $this->addressAddition        = $reader->getValue('returnshipment_addition');
        $this->dispatchingInformation = $reader->getValue('returnshipment_dispatchinfo');
        $this->zip                    = $reader->getValue('returnshipment_zip');
        $this->city                   = $reader->getValue('returnshipment_city');
        $this->country                = $reader->getValue('returnshipment_country');
        $this->countryISOCode         = $reader->getValue('returnshipment_countrycode');
        $this->state                  = $reader->getValue('returnshipment_region');

        $this->phone         = $reader->getValue('returnshipment_phone');
        $this->email         = $reader->getValue('returnshipment_email');
        $this->contactPerson = $reader->getValue('returnshipment_person');
    }
}
