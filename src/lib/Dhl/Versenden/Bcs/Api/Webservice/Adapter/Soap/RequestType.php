<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Webservice\Adapter\Soap;

use Dhl\Versenden\Bcs\Api\Webservice\RequestData;

interface RequestType
{
    public static function prepare(RequestData $requestData);
}
