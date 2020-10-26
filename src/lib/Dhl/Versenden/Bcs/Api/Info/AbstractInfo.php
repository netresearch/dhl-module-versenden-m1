<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Info;

abstract class AbstractInfo implements \JsonSerializable, UnserializableInterface
{
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

    /**
     * @param $json
     * @return AbstractInfo|null
     */
    public static function fromJson($json)
    {
        $object = json_decode($json);
        return static::fromObject($object);
    }
}
