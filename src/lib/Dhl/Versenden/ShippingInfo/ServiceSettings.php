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
 * ServiceSettings
 *
 * @category Dhl
 * @package  Dhl\Versenden\ShippingInfo
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class ServiceSettings
{
    /** @var bool */
    public $dayOfDelivery;
    /** @var bool */
    public $deliveryTimeFrame;
    /** @var bool */
    public $preferredLocation;
    /** @var bool */
    public $preferredNeighbour;
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

    public function __construct(\stdClass $object = null)
    {
        if ($object) {
            $this->dayOfDelivery = isset($object->dayOfDelivery) ? $object->dayOfDelivery : false;
            $this->deliveryTimeFrame = isset($object->deliveryTimeFrame) ? $object->deliveryTimeFrame : false;
            $this->preferredLocation = isset($object->preferredLocation) ? $object->preferredLocation : false;
            $this->preferredNeighbour = isset($object->preferredNeighbour) ? $object->preferredNeighbour : false;
            $this->parcelAnnouncement = isset($object->parcelAnnouncement) ? $object->parcelAnnouncement : 0;
            $this->visualCheckOfAge = isset($object->visualCheckOfAge) ? $object->visualCheckOfAge : false;
            $this->returnShipment = isset($object->returnShipment) ? $object->returnShipment : false;
            $this->insurance = isset($object->insurance) ? $object->insurance : false;
            $this->bulkyGoods = isset($object->bulkyGoods) ? $object->bulkyGoods : false;
        }
    }
}
