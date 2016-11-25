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
 * @package   Dhl\Versenden\Bcs\Api\Info
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
namespace Dhl\Versenden\Bcs\Api;

/**
 * Info
 *
 * @category Dhl
 * @package  Dhl\Versenden\Bcs\Api\Info
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Info extends Info\AbstractInfo
{
    const SCHEMA_VERSION = '1.0';

    /** @var string */
    public $schemaVersion;
    /** @var Info\Receiver */
    public $receiver;
    /** @var Info\Services */
    public $services;

    /**
     * Info constructor.
     */
    public function __construct()
    {
        $this->schemaVersion = self::SCHEMA_VERSION;
        $this->receiver = new Info\Receiver();
        $this->services = new Info\Services();
    }

    /**
     * @return Info\Receiver
     */
    public function getReceiver()
    {
        return $this->receiver;
    }

    /**
     * @return Info\Services
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * @param \stdClass $object
     * @return Info|null
     */
    public static function fromObject(\stdClass $object)
    {
        if (!isset($object->schemaVersion) || ($object->schemaVersion !== self::SCHEMA_VERSION)) {
            return null;
        }

        $info = new self();
        if (isset($object->receiver)) {
            $info->receiver = Info\Receiver::fromObject($object->receiver);
        }

        if (isset($object->services)) {
            $info->services = Info\Services::fromObject($object->services);
        }

        return $info;
    }

    public function __toString()
    {
        return Info\Serializer::serialize($this);
    }
}
