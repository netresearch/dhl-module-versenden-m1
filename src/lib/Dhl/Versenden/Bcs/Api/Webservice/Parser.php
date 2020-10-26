<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Webservice;

interface Parser
{
    /**
     * @param \stdClass $response
     * @return \stdClass
     */
    public function parse($response);
}
