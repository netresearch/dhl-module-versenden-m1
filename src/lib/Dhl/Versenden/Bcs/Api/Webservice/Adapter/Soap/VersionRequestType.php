<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Webservice\Adapter\Soap;

use Dhl\Versenden\Bcs\Soap as VersendenApi;
use Dhl\Versenden\Bcs\Api\Webservice\RequestData;

class VersionRequestType implements RequestType
{
    /**
     * @param RequestData\Version $requestData
     * @return VersendenApi\Version
     */
    public static function prepare(RequestData $requestData)
    {
        $requestType = new VersendenApi\Version(
            $requestData->getMajorRelease(),
            $requestData->getMinorRelease(),
            $requestData->getBuild()
        );

        return $requestType;
    }
}
