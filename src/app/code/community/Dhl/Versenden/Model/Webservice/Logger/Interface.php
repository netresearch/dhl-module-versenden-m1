<?php

/**
 * See LICENSE.md for license details.
 */

use \Dhl\Versenden\Bcs\Api\Webservice\Adapter;

interface Dhl_Versenden_Model_Webservice_Logger_Interface
{
    /**
     * @param Adapter $adapter
     */
    public function debug(Adapter $adapter);

    /**
     * @param Adapter $adapter
     */
    public function error(Adapter $adapter);
}
