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
namespace Dhl\Versenden;
use Dhl\Versenden\ShippingInfo\Receiver;
use Dhl\Versenden\ShippingInfo\ServiceSettings;

/**
 * Service
 *
 * @category Dhl
 * @package  Dhl\Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class ShippingInfo
{
    /** @var ServiceSettings */
    public $serviceSettings;
    /** @var Receiver */
    public $shippingAddress;

    public function __construct(ServiceSettings $settings = null, Receiver $receiver = null)
    {
        $this->serviceSettings = $settings;
        $this->shippingAddress = $receiver;
    }

    /**
     * Convert the current service settings to a string representation.
     * @return string
     */
    public function getJson()
    {
        return \Zend_Json::encode($this, true);
    }

    /**
     * Load the shipment settings from a string representation.
     *
     * @param string $json
     * @throws \Zend_Json_Exception
     */
    public function setJson($json)
    {
        $stdObject = \Zend_Json::decode($json, \Zend_Json::TYPE_OBJECT);

        $this->serviceSettings = new ServiceSettings($stdObject->serviceSettings);
        $this->shippingAddress = new Receiver($stdObject->shippingAddress);
    }
}
