<?php

/**
 * See LICENSE.md for license details.
 */

/**
 * PII and Credential Sanitizer
 *
 * Masks sensitive data in log messages and context arrays for GDPR compliance.
 * Uses configurable field patterns to identify and mask personal data, credentials,
 * and other sensitive information before it reaches log files.
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   DHL Paket <dhl.api@dhl.com>
 * @license  https://opensource.org/licenses/osl-3.0.php Open Software License 3.0
 */
class Dhl_Versenden_Model_Logger_Sanitizer
{
    /**
     * Field name patterns that contain personal data (case-insensitive partial match)
     *
     * @var string[]
     */
    protected $_sensitiveFields = [
        // Personal identification
        'name', 'name1', 'name2', 'name3', 'firstname', 'lastname', 'surname', 'givenname',
        'email', 'phone', 'mobile', 'fax', 'telephone',
        'contactperson', 'recipient', 'shipper', 'receiver', 'consignee',

        // Address data
        'street', 'streetname', 'streetnumber', 'addressaddition', 'addressline',
        'address', 'addresshouse', 'city', 'state', 'company', 'dispatchinginfo',

        // Financial data
        'iban', 'bic', 'accountowner', 'accountholder', 'accountreference',
        'banknumber', 'accountnumber', 'bankname',

        // DHL-specific identifiers
        'postnumber', 'packstationnumber', 'postfilialenumber',

        // Identity verification
        'dateofbirth', 'dob', 'birthdate',
    ];

    /**
     * HTTP header names to completely mask (case-insensitive exact match)
     *
     * @var string[]
     */
    protected $_sensitiveHeaders = [
        'authorization',
        'dpdhl-user-authentication-token',
        'dhl-api-key',
        'x-api-key',
        'api-key',
        'x-auth-token',
    ];

    /**
     * Patterns for sensitive data in string content
     *
     * @var array Format: ['pattern' => 'replacement']
     */
    protected $_stringPatterns = [
        // HTTP Authorization headers
        '/Authorization:\s*Basic\s+[A-Za-z0-9+\/=]+/i' => 'Authorization: Basic ***',
        '/Authorization:\s*Bearer\s+[A-Za-z0-9\-._~+\/]+=*/i' => 'Authorization: Bearer ***',

        // API key headers
        '/(dhl-api-key|dpdhl-user-authentication-token|x-api-key):\s*[^\s\r\n]+/i' => '$1: ***',

        // Email addresses (partial masking for debugging)
        '/([a-z0-9._%+-]+)@([a-z0-9.-]+\.[a-z]{2,})/i' => '***@$2',
    ];

    /**
     * Sanitize string content (for log messages)
     *
     * Applies regex patterns to mask sensitive data in string content,
     * typically used for HTTP messages, error messages, or other text logs.
     * Also detects and sanitizes JSON payloads embedded in HTTP message bodies.
     *
     * @param mixed $value Value to sanitize
     *
     * @return mixed Sanitized value (unchanged if not string)
     */
    public function sanitizeString($value)
    {
        if (!is_string($value)) {
            return $value;
        }

        // Apply regex patterns for HTTP headers and inline credentials
        foreach ($this->_stringPatterns as $pattern => $replacement) {
            $value = preg_replace($pattern, $replacement, $value);
        }

        // Sanitize JSON payloads in HTTP message bodies (SDK logging)
        $value = $this->_sanitizeJsonInHttpMessage($value);

        return $value;
    }

    /**
     * Sanitize JSON payloads in HTTP message strings
     *
     * SDK's FullHttpMessageFormatter logs complete HTTP messages with JSON bodies.
     * This method detects JSON in HTTP bodies and applies array sanitization.
     *
     * Example input:
     * "POST /api HTTP/1.1\nHost: example.com\n\n{\"name\":\"John\",\"email\":\"john@example.com\"}"
     *
     * @param string $message HTTP message string
     *
     * @return string Message with sanitized JSON payload
     */
    protected function _sanitizeJsonInHttpMessage($message)
    {
        // Pattern: JSON after HTTP headers (after double newline or after "Content-Length: N\n\n")
        // Matches: \n\n{...} or \r\n\r\n{...} at end of message
        if (preg_match('/(\r?\n\r?\n)(\{.+\})\s*$/s', $message, $matches)) {
            $separator = $matches[1];  // \n\n or \r\n\r\n
            $jsonString = $matches[2]; // JSON payload

            // Attempt to decode JSON
            $data = json_decode($jsonString, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
                // Sanitize the decoded data
                $sanitized = $this->sanitizeArray($data);

                // Re-encode with same formatting as our structured logging
                $sanitizedJson = json_encode($sanitized, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

                // Replace original JSON with sanitized version
                return str_replace($jsonString, $sanitizedJson, $message);
            }
        }

        return $message;
    }

    /**
     * Sanitize array recursively
     *
     * Recursively processes arrays to mask sensitive field values while
     * preserving data structure. Handles nested arrays, objects, and mixed types.
     *
     * @param array $data Data array to sanitize
     *
     * @return array Sanitized array with same structure
     */
    public function sanitizeArray(array $data)
    {
        $sanitized = [];

        foreach ($data as $key => $value) {
            // PSR-3 standard: exception objects in context must be preserved
            if ($key === 'exception' && $value instanceof \Exception) {
                $sanitized[$key] = $value;  // Keep Exception objects intact
            } elseif (is_array($value)) {
                // Nested array: always recurse (even if key name seems sensitive - it's a container)
                $sanitized[$key] = $this->sanitizeArray($value);
            } elseif (is_object($value)) {
                // Object: attempt to convert and sanitize (container, not leaf data)
                $sanitized[$key] = $this->_sanitizeObject($value);
            } elseif ($this->_isSensitiveField($key)) {
                // Sensitive scalar field: mask the value
                $sanitized[$key] = $this->_maskValue($value);
            } else {
                // Safe scalar value: keep as-is
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }

    /**
     * Check if field name indicates sensitive data
     *
     * @param mixed $fieldName Field name to check
     *
     * @return bool True if field name matches sensitive pattern
     */
    protected function _isSensitiveField($fieldName)
    {
        $fieldLower = strtolower((string) $fieldName);

        // Check field name patterns
        foreach ($this->_sensitiveFields as $sensitive) {
            if (str_contains($fieldLower, strtolower($sensitive))) {
                return true;
            }
        }

        // Check sensitive header names (exact match)
        foreach ($this->_sensitiveHeaders as $header) {
            if ($fieldLower === strtolower($header)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Mask sensitive value based on type
     *
     * Strategy: Keep first 2 characters for debugging, mask rest with asterisks.
     * This allows identifying which data is present without exposing full values.
     *
     * @param mixed $value Value to mask
     *
     * @return string Masked value
     */
    protected function _maskValue($value)
    {
        if (is_string($value) && strlen($value) > 0) {
            if (strlen($value) <= 2) {
                return '**';
            }
            // Keep first 2 chars: "Müller" → "Mü****"
            return substr($value, 0, 2) . str_repeat('*', min(8, strlen($value) - 2));
        }

        if (is_numeric($value)) {
            return '***';
        }

        if (is_bool($value)) {
            // Booleans are typically not sensitive, return string representation
            return $value ? 'true' : 'false';
        }

        if (is_null($value)) {
            return '';
        }

        // Arrays and objects shouldn't reach here, but mask if they do
        return '***';
    }

    /**
     * Sanitize object by converting to array or string
     *
     * @param object $object Object to sanitize
     *
     * @return mixed Sanitized representation
     */
    protected function _sanitizeObject($object)
    {
        // Try array conversion
        if (method_exists($object, 'toArray')) {
            return $this->sanitizeArray($object->toArray());
        }

        // Try JSON serialization (common for API objects)
        if ($object instanceof \JsonSerializable) {
            $data = $object->jsonSerialize();
            return is_array($data) ? $this->sanitizeArray($data) : $this->sanitizeString((string) $data);
        }

        // Try string conversion
        if (method_exists($object, '__toString')) {
            return $this->sanitizeString((string) $object);
        }

        // Fallback: log class name only
        return get_class($object) . ' (object)';
    }
}
