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
 * @package   Dhl\Versenden\Config
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
namespace Dhl\Versenden\Config;
use Dhl\Versenden\Config as ConfigReader;
use Dhl\Versenden\Config\Data as ConfigData;
/**
 * Service
 *
 * @category Dhl
 * @package  Dhl\Versenden\Config
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Service extends ConfigData
{
    /** @var bool */
    public $dayOfDelivery;
    /** @var bool */
    public $deliveryTimeFrame;
    /** @var bool */
    public $preferredLocation;
    /** @var bool */
    public $preferredNeighbour;
    /** @var bool */
    public $packstation;
    /** @var int */
    public $parcelAnnouncement;
    /** @var bool */
    public $visualCheckOfAge;
    /** @var bool */
    public $returnShipment;
    /** @var bool */
    public $insurance;
    /** @var bool */
    public $bulkyGoods;

    /**
     * Shift data from config array to properties.
     *
     * @param ConfigReader $reader
     */
    public function loadValues(ConfigReader $reader)
    {
        $this->dayOfDelivery      = (bool)$reader->getValue('service_dayofdelivery_enabled');
        $this->deliveryTimeFrame  = (bool)$reader->getValue('service_deliverytimeframe_enabled');
        $this->preferredLocation  = (bool)$reader->getValue('service_preferredlocation_enabled');
        $this->preferredNeighbour = (bool)$reader->getValue('service_preferredneighbour_enabled');
        $this->packstation        = (bool)$reader->getValue('service_packstation_enabled');
        $this->parcelAnnouncement = (int)$reader->getValue('service_parcelannouncement_enabled');
        $this->visualCheckOfAge   = (bool)$reader->getValue('service_visualcheckofage_enabled');
        $this->returnShipment     = (bool)$reader->getValue('service_returnshipment_enabled');
        $this->insurance          = (bool)$reader->getValue('service_insurance_enabled');
        $this->bulkyGoods         = (bool)$reader->getValue('service_bulkygoods_enabled');
    }
}
