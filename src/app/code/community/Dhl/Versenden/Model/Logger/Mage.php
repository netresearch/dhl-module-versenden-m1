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
 * Dhl_Versenden_Model_Logger_Mage
 *
 * @category  Dhl
 * @package   Dhl_Versenden
 * @author    Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
class Dhl_Versenden_Model_Logger_Mage extends \Psr\Log\AbstractLogger
{
    /** @var Dhl_Versenden_Model_Logger_Writer */
    protected $_writer;

    /** @var string */
    protected $_file = 'dhl_versenden.log';

    /** @var int[] */
    protected $_levelMapping = array(
        \Psr\Log\LogLevel::EMERGENCY => Zend_Log::EMERG,
        \Psr\Log\LogLevel::ALERT     => Zend_Log::ALERT,
        \Psr\Log\LogLevel::CRITICAL  => Zend_Log::CRIT,
        \Psr\Log\LogLevel::ERROR     => Zend_Log::ERR,
        \Psr\Log\LogLevel::WARNING   => Zend_Log::WARN,
        \Psr\Log\LogLevel::NOTICE    => Zend_Log::NOTICE,
        \Psr\Log\LogLevel::INFO      => Zend_Log::INFO,
        \Psr\Log\LogLevel::DEBUG     => Zend_Log::DEBUG,
    );

    /**
     * Dhl_Versenden_Model_Logger_Mage constructor.
     * @param Dhl_Versenden_Model_Logger_Writer $writer
     * @param string $file
     */
    public function __construct(Dhl_Versenden_Model_Logger_Writer $writer, $file = '')
    {
        $this->_writer = $writer;
        if ($file) {
            $this->_file = $file;
        }
    }

    /**
     * Replace placeholders in context
     * @link https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md#12-message
     *
     * @param $message
     * @param mixed[] $context
     *
     * @return string
     */
    protected function interpolate($message, array $context = array())
    {
        // build a replacement array with braces around the context keys
        $replace = array();
        foreach ($context as $key => $val) {
            // check that the value can be casted to string
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            }
        }

        // interpolate replacement values into the message and return
        return strtr($message, $replace);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param string $level
     * @param string $message
     * @param mixed[] $context
     *
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        if (isset($context['exception'])) {
            $this->_writer->logException($context['exception']);
        }

        $message = $this->interpolate($message, $context);
        $this->_writer->log($message, $this->_levelMapping[$level], $this->_file);
    }
}
