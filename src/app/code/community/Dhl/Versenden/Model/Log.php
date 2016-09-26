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
    protected $file = 'dhl_versenden.log';

    /** @var \Psr\Log\LoggerInterface */
    protected $psrLogger;

    /** @var Dhl_Versenden_Model_Config */
    protected $config;

    /**
     * Dhl_Versenden_Model_Log constructor.
     *
     * @param mixed[] $args
     * @throws Mage_Core_Exception
     */
    public function __construct($args)
    {
        if (!isset($args['config']) || !$args['config'] instanceof Dhl_Versenden_Model_Config) {
            throw new Mage_Core_Exception('missing or invalid argument: config');
        }

        $this->psrLogger = new \Psr\Log\NullLogger();
        $this->config = $args['config'];
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
        $this->psrLogger = $logger;
    }

    /**
     * Check config and pass debug messages to PSR Logger
     *
     * @param string $message
     * @param mixed[] $context
     */
    public function debug($message, array $context = array())
    {
        if ($this->config->isLoggingEnabled(Zend_Log::DEBUG)) {
            $this->psrLogger->debug($message, $context);
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
        if ($this->config->isLoggingEnabled(Zend_Log::ERR)) {
            $this->psrLogger->error($message, $context);
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
        if ($this->config->isLoggingEnabled(Zend_Log::WARN)) {
            $this->psrLogger->warning($message, $context);
        }
    }
}
