<?php

/**
 * See LICENSE.md for license details.
 */

/**
 * Sanitizing PSR-3 Logger Decorator
 *
 * Wraps any PSR-3 logger and sanitizes all messages and context data before logging.
 * This ensures PII and credential data is masked in log files for GDPR compliance.
 *
 * The sanitizing layer is transparent to clients - they log normally, and sanitization
 * happens automatically. Module configuration (logging_enabled, log_level) is still
 * respected through the underlying ConfigAware logger.
 *
 * Usage:
 * <code>
 * $sanitizer = new Dhl_Versenden_Model_Logger_Sanitizer();
 * $sanitizingLogger = new Dhl_Versenden_Model_Logger_Sanitizing($configAwareLogger, $sanitizer);
 * $sanitizingLogger->debug('API Request', ['headers' => ['Authorization' => 'Bearer secret']]);
 * // Logs: API Request {"headers":{"Authorization":"***"}}
 * </code>
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   DHL Paket <dhl.api@dhl.com>
 * @license  https://opensource.org/licenses/osl-3.0.php Open Software License 3.0
 */
class Dhl_Versenden_Model_Logger_Sanitizing extends \Psr\Log\AbstractLogger
{
    /**
     * Underlying PSR-3 logger (typically ConfigAware → Mage → Writer chain)
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * Sanitizer for PII and credential masking
     *
     * @var Dhl_Versenden_Model_Logger_Sanitizer
     */
    protected $_sanitizer;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface              $logger    Underlying logger to delegate to
     * @param Dhl_Versenden_Model_Logger_Sanitizer $sanitizer Sanitizer for data masking
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        Dhl_Versenden_Model_Logger_Sanitizer $sanitizer
    ) {
        $this->_logger = $logger;
        $this->_sanitizer = $sanitizer;
    }

    /**
     * Logs with an arbitrary level, sanitizing message and context first
     *
     * This method is called by all PSR-3 log level methods (debug, info, error, etc.)
     * and applies sanitization before delegating to the underlying logger chain.
     *
     * Sanitization process:
     * 1. Message: Regex patterns mask credentials in HTTP messages
     * 2. Context: Recursive array sanitization masks sensitive fields
     * 3. Delegation: Sanitized data passed to ConfigAware → Mage → Writer
     *
     * @param string  $level   PSR-3 log level (debug, info, warning, error, etc.)
     * @param string  $message Log message (may contain HTTP messages, error text, etc.)
     * @param mixed[] $context Associative array of contextual data (headers, response bodies, etc.)
     *
     * @return null
     */
    public function log($level, $message, array $context = [])
    {
        // Sanitize message string (handles HTTP messages with inline credentials)
        $sanitizedMessage = $this->_sanitizer->sanitizeString($message);

        // Sanitize context array recursively (handles structured data)
        $sanitizedContext = $this->_sanitizer->sanitizeArray($context);

        // Delegate to underlying logger (respects module config)
        $this->_logger->log($level, $sanitizedMessage, $sanitizedContext);
    }
}
