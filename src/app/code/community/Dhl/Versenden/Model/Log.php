<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Model_Log implements Psr\Log\LoggerAwareInterface
{
    protected $_file = 'dhl_versenden.log';

    /** @var \Psr\Log\LoggerInterface */
    protected $_psrLogger;

    /** @var Dhl_Versenden_Model_Config */
    protected $_config;

    /**
     * Dhl_Versenden_Model_Log constructor.
     *
     * @param mixed[] $args
     * @throws Mage_Core_Exception
     */
    public function __construct($args)
    {
        if (!isset($args['config']) || !$args['config'] instanceof Dhl_Versenden_Model_Config) {
            Mage::throwException('missing or invalid argument: config');
        }

        $this->_psrLogger = new \Psr\Log\NullLogger();
        $this->_config = $args['config'];
    }

    /**
     * Sets a logger instance on the object.
     *
     * @param \Psr\Log\LoggerInterface $logger
     *
     * @return null
     */
    public function setLogger(\Psr\Log\LoggerInterface $logger)
    {
        $this->_psrLogger = $logger;
    }

    /**
     * Check config and pass debug messages to PSR Logger
     *
     * @param string $message
     * @param mixed[] $context
     */
    public function debug($message, array $context = array())
    {
        if ($this->_config->isLoggingEnabled(Zend_Log::DEBUG)) {
            $this->_psrLogger->debug($message, $context);
        }
    }

    /**
     * Check config and pass errors to PSR Logger
     *
     * @param $message
     * @param mixed[] $context
     */
    public function error($message, array $context = array())
    {
        if ($this->_config->isLoggingEnabled(Zend_Log::ERR)) {
            $this->_psrLogger->error($message, $context);
        }
    }

    /**
     * Check config and pass warnings to PSR Logger
     *
     * @param $message
     * @param mixed[] $context
     */
    public function warning($message, array $context = array())
    {
        if ($this->_config->isLoggingEnabled(Zend_Log::WARN)) {
            $this->_psrLogger->warning($message, $context);
        }
    }
}
