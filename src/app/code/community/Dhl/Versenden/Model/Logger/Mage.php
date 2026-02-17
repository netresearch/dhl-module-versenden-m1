<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Model_Logger_Mage extends \Psr\Log\AbstractLogger
{
    /** @var Dhl_Versenden_Model_Logger_Writer */
    protected $_writer;

    /** @var string */
    protected $_file = 'dhl_versenden.log';

    /** @var int[] */
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
    protected function interpolate($message, array $context = [])
    {
        // build a replacement array with braces around the context keys
        $replace = [];
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
    public function log($level, $message, array $context = [])
    {
        if (isset($context['exception'])) {
            $this->_writer->logException($context['exception']);
            // Remove exception from context to avoid JSON serialization issues
            unset($context['exception']);
        }

        // Interpolate PSR-3 placeholders: "User {user}" with ['user' => 'John'] → "User John"
        $message = $this->interpolate($message, $context);

        // Append context as JSON if present (for structured logging)
        if (!empty($context)) {
            $contextJson = json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $message .= ' ' . $contextJson;
        }

        // Force log = true to bypass Magento's dev/log/active check
        // We've already validated configuration in ConfigAware wrapper
        $this->_writer->log($message, $this->_levelMapping[$level], $this->_file, true);
    }
}
