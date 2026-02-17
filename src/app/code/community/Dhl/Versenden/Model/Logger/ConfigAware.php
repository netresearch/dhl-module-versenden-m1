<?php

/**
 * See LICENSE.md for license details.
 */

/**
 * Config-aware PSR-3 Logger
 *
 * This logger implements PSR-3 LoggerInterface and checks module configuration
 * (logging_enabled and log_level) before delegating to the underlying logger.
 *
 * Required for SDK compatibility: SDKs require pure PSR-3 loggers, not wrappers.
 * The config check happens inside the PSR-3 logger itself.
 */
class Dhl_Versenden_Model_Logger_ConfigAware extends \Psr\Log\AbstractLogger
{
    /** @var \Psr\Log\LoggerInterface */
    protected $_delegateLogger;

    /** @var Dhl_Versenden_Model_Config */
    protected $_config;

    /** @var int[] Map PSR-3 log levels to Zend_Log levels for config check */
    protected $_levelMapping = [
        \Psr\Log\LogLevel::EMERGENCY => Zend_Log::EMERG,
        \Psr\Log\LogLevel::ALERT     => Zend_Log::ALERT,
        \Psr\Log\LogLevel::CRITICAL  => Zend_Log::CRIT,
        \Psr\Log\LogLevel::ERROR     => Zend_Log::ERR,
        \Psr\Log\LogLevel::WARNING   => Zend_Log::WARN,
        \Psr\Log\LogLevel::NOTICE    => Zend_Log::NOTICE,
        \Psr\Log\LogLevel::INFO      => Zend_Log::INFO,
        \Psr\Log\LogLevel::DEBUG     => Zend_Log::DEBUG,
    ];

    /**
     * @param \Psr\Log\LoggerInterface $delegateLogger The actual logger that writes to file
     * @param Dhl_Versenden_Model_Config $config Configuration to check logging settings
     */
    public function __construct(\Psr\Log\LoggerInterface $delegateLogger, Dhl_Versenden_Model_Config $config)
    {
        $this->_delegateLogger = $delegateLogger;
        $this->_config = $config;
    }

    /**
     * Logs with an arbitrary level.
     *
     * Checks configuration before delegating to underlying logger.
     *
     * @param string $level
     * @param string $message
     * @param mixed[] $context
     *
     * @return null
     */
    public function log($level, $message, array $context = [])
    {
        // Check if logging is enabled for this level
        $zendLevel = $this->_levelMapping[$level] ?? Zend_Log::DEBUG;

        if ($this->_config->isLoggingEnabled($zendLevel)) {
            $this->_delegateLogger->log($level, $message, $context);
        }
    }
}
