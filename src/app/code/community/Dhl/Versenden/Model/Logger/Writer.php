<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Model_Logger_Writer
{
    /**
     * Log wrapper
     *
     * @param string $message
     * @param int $level
     * @param string $file
     * @param bool $forceLog
     * @return void
     */
    public function log($message, $level = null, $file = '', $forceLog = false)
    {
        Mage::log($message, $level, $file, $forceLog);
    }

    /**
     * Log exception wrapper
     *
     * @param Exception $e
     * @return void
     */
    public function logException(Exception $e)
    {
        Mage::logException($e);
    }
}
