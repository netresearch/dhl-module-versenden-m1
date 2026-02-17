<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\ParcelDe;

class Info extends Info\AbstractInfo
{
    public const SCHEMA_VERSION = '1.0';

    /**
     * @var string
     */
    public $schemaVersion;

    /**
     * @var Info\Receiver
     */
    public $receiver;

    /**
     * @var Info\Services
     */
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

    /**
     * @return string
     */
    public function __toString()
    {
        return Info\Serializer::serialize($this);
    }
}
