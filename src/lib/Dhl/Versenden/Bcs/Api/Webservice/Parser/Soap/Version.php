<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Webservice\Parser\Soap;

use \Dhl\Versenden\Bcs\Soap as VersendenApi;
use \Dhl\Versenden\Bcs\Api\Webservice;

class Version implements Webservice\Parser
{
    /**
     * @param VersendenApi\GetVersionResponse $response
     * @return Webservice\ResponseData\Version
     */
    public function parse($response)
    {
        $version = sprintf(
            '%s.%s.%s',
            $response->getVersion()->getMajorRelease(),
            $response->getVersion()->getMinorRelease(),
            $response->getVersion()->getBuild()
        );
        return new Webservice\ResponseData\Version($version);
    }
}
