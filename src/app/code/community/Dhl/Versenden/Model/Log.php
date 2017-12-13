<?php
/**
 * Dhl Versenden
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer versions in the future.
 *
 * PHP version 5
 *
 * @category  Dhl
 * @package   Dhl_Versenden
 * @author    Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */

/**
 * Dhl_Versenden_Model_Log
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
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
