<?php

/**
 * See LICENSE.md for license details.
 */

use \Dhl\Versenden\Bcs\Api\Webservice\Adapter;
use \Dhl\Versenden\Bcs\Api\Webservice\Adapter\Soap as SoapAdapter;

class Dhl_Versenden_Model_Webservice_Logger_Soap
    extends Dhl_Versenden_Model_Webservice_Logger_Abstract
{
    /**
     * @param SoapAdapter $adapter
     */
    public function debug(Adapter $adapter)
    {
        $this->_log->debug($adapter->getClient()->__getLastRequest());
        $this->_log->debug($adapter->getClient()->__getLastResponseHeaders());
        $this->_log->debug($adapter->getClient()->__getLastResponse());
    }

    /**
     * @param SoapAdapter $adapter
     */
    public function error(Adapter $adapter)
    {
        $this->_log->error($adapter->getClient()->__getLastRequest());
        $this->_log->error($adapter->getClient()->__getLastResponseHeaders());
        $this->_log->error($adapter->getClient()->__getLastResponse());
    }


    /**
     * @param SoapAdapter $adapter
     */
    public function warning(Adapter $adapter)
    {
        $this->_log->warning($adapter->getClient()->__getLastRequest());
        $this->_log->warning($adapter->getClient()->__getLastResponseHeaders());
        $this->_log->warning($adapter->getClient()->__getLastResponse());
    }
}
