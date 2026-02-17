<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\ParcelDe\Info;

use Dhl\Versenden\ParcelDe\Info;

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
        $result = Info::fromJson($serialized);
        /** @var Info|null $result */
        return $result;
    }
}
