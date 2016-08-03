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
namespace Dhl\Versenden\Webservice\RequestData;
use Dhl\Versenden\Webservice\RequestData\ShipmentOrder;

/**
 * ShippingInfo
 *
 * @category Dhl
 * @package  Dhl\Versenden\Webservice\RequestData
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class ShippingInfo implements \JsonSerializable
{
    /** @var ShipmentOrder\Receiver */
    private $receiver;
    /** @var ShipmentOrder\Settings\ServiceSettings */
    private $serviceSettings;
    /** @var ShipmentOrder\Settings\ShipmentSettings */
    private $shipmentSettings;
    /** @var string */
    private $schemaVersion = ObjectMapper::SCHEMA_VERSION;

    public function __construct(
        ShipmentOrder\Receiver $receiver,
        ShipmentOrder\Settings\ServiceSettings $serviceSettings,
        ShipmentOrder\Settings\ShipmentSettings $shipmentSettings = null
    ) {
        $this->receiver         = $receiver;
        $this->serviceSettings  = $serviceSettings;
        $this->shipmentSettings = $shipmentSettings;
    }

    /**
     * @return ShipmentOrder\Receiver
     */
    public function getReceiver()
    {
        return $this->receiver;
    }

    /**
     * @return ShipmentOrder\Settings\ServiceSettings
     */
    public function getServiceSettings()
    {
        return $this->serviceSettings;
    }

    /**
     * @return ShipmentOrder\Settings\ShipmentSettings
     */
    public function getShipmentSettings()
    {
        return $this->shipmentSettings;
    }

    /**
     * @return string
     */
    public function getSchemaVersion()
    {
        return $this->schemaVersion;
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
