<?php

/**
 * See LICENSE.md for license details.
 */

abstract class Dhl_Versenden_Model_Webservice_Logger_Abstract
    implements Dhl_Versenden_Model_Webservice_Logger_Interface
{
    /** @var Dhl_Versenden_Model_Log */
    protected $_log;

    /**
     * Dhl_Versenden_Model_Webservice_Logger_Abstract constructor.
     * @param Dhl_Versenden_Model_Log $log
     */
    public function __construct(Dhl_Versenden_Model_Log $log)
    {
        $this->_log = $log;
    }
}
