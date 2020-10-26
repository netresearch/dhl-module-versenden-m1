<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Info;

use Dhl\Versenden\Bcs\Api\Info;

interface UnserializableInterface
{
    /**
     * @param $json
     * @return AbstractInfo|null
     */
    public static function fromJson($json);

    /**
     * @param \stdClass $object
     * @return AbstractInfo|null
     */
    public static function fromObject(\stdClass $object);
}
