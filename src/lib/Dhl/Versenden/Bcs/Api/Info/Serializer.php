<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Info;
use Dhl\Versenden\Bcs\Api\Info;

class Serializer
{
    /**
     * @param Info $info
     * @return string
     */
    public static function serialize(Info $info)
    {
        return json_encode($info, JSON_FORCE_OBJECT);
    }

    /**
     * @param $serialized
     * @return Info|null
     */
    public static function unserialize($serialized)
    {
        return Info::fromJson($serialized);
    }
}
