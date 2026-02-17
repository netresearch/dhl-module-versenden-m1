<?php

/**
 * See LICENSE.md for license details.
 */

/**
 * Sanitizer Unit Tests
 *
 * Tests PII and credential masking to ensure GDPR compliance.
 * Validates that sensitive data is properly masked while non-sensitive data remains intact.
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   DHL Paket <dhl.api@dhl.com>
 * @license  https://opensource.org/licenses/osl-3.0.php Open Software License 3.0
 */
class Dhl_Versenden_Test_Model_Logger_SanitizerTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Sanitizer instance under test
     *
     * @var Dhl_Versenden_Model_Logger_Sanitizer
     */
    protected $_sanitizer;

    /**
     * Set up test fixture
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->_sanitizer = new Dhl_Versenden_Model_Logger_Sanitizer();
    }

    /**
     * @test
     */
    public function sanitizeStringMasksAuthorizationHeaders()
    {
        $input = "Authorization: Basic dXNlcjpwYXNz\nContent-Type: application/json";
        $result = $this->_sanitizer->sanitizeString($input);

        static::assertStringContainsString('Authorization: Basic ***', $result);
        static::assertStringNotContainsString('dXNlcjpwYXNz', $result);
        static::assertStringContainsString('Content-Type: application/json', $result);
    }

    /**
     * @test
     */
    public function sanitizeStringMasksBearerTokens()
    {
        $input = 'Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIn0.dozjgNryP4J3jVmNHl0w5N_XgL0n3I9PlFUP0THsR8U';
        $result = $this->_sanitizer->sanitizeString($input);

        static::assertStringContainsString('Authorization: Bearer ***', $result);
        static::assertStringNotContainsString('eyJhbG', $result);
    }

    /**
     * @test
     */
    public function sanitizeStringMasksApiKeyHeaders()
    {
        $input = "dhl-api-key: my-secret-api-key\nDPDHL-User-Authentication-Token: another-secret";
        $result = $this->_sanitizer->sanitizeString($input);

        static::assertStringContainsString('dhl-api-key: ***', $result);
        static::assertStringContainsString('DPDHL-User-Authentication-Token: ***', $result);
        static::assertStringNotContainsString('my-secret-api-key', $result);
        static::assertStringNotContainsString('another-secret', $result);
    }

    /**
     * @test
     */
    public function sanitizeStringMasksEmailAddresses()
    {
        $input = 'Contact: max.mustermann@example.com for assistance';
        $result = $this->_sanitizer->sanitizeString($input);

        static::assertStringContainsString('***@example.com', $result);
        static::assertStringNotContainsString('max.mustermann', $result);
    }

    /**
     * @test
     */
    public function sanitizeArrayMasksPersonalDataFields()
    {
        $input = [
            'name' => 'Max Mustermann',
            'email' => 'max@example.com',
            'postalCode' => '53113',
            'product' => 'V01PAK',
        ];

        $result = $this->_sanitizer->sanitizeArray($input);

        static::assertArrayHasKey('name', $result);
        static::assertArrayHasKey('email', $result);
        static::assertArrayHasKey('postalCode', $result);
        static::assertArrayHasKey('product', $result);

        // Sensitive fields masked
        static::assertStringStartsWith('Ma', $result['name']);
        static::assertStringContainsString('*', $result['name']);
        static::assertNotEquals('Max Mustermann', $result['name']);

        static::assertStringContainsString('*', $result['email']);
        static::assertNotEquals('max@example.com', $result['email']);

        // Non-sensitive fields unchanged
        static::assertEquals('53113', $result['postalCode']);
        static::assertEquals('V01PAK', $result['product']);
    }

    /**
     * @test
     */
    public function sanitizeArrayMasksNestedData()
    {
        $input = [
            'from' => [
                'company' => 'DHL Paket GmbH',
                'streetName' => 'Charles-de-Gaulle-Straße',
                'city' => 'Bonn',
                'postalCode' => '53113',
            ],
            'to' => [
                'name' => 'Max Mustermann',
                'email' => 'max@example.com',
            ],
        ];

        $result = $this->_sanitizer->sanitizeArray($input);

        // Check nested structure preserved
        static::assertArrayHasKey('from', $result);
        static::assertArrayHasKey('to', $result);
        static::assertIsArray($result['from']);
        static::assertIsArray($result['to']);

        // Check sensitive fields masked at all levels
        static::assertStringContainsString('*', $result['from']['company']);
        static::assertStringContainsString('*', $result['from']['streetName']);
        static::assertStringContainsString('*', $result['from']['city']); // city is also sensitive
        static::assertStringContainsString('*', $result['to']['name']);
        static::assertStringContainsString('*', $result['to']['email']);

        // Check non-sensitive fields unchanged (postalCode is not in sensitive list)
        static::assertEquals('53113', $result['from']['postalCode']);
    }

    /**
     * @test
     */
    public function sanitizeArrayMasksFinancialData()
    {
        $input = [
            'codAmount' => 150.50,
            'accountOwner' => 'Max Mustermann',
            'iban' => 'DE89370400440532013000',
            'bic' => 'COBADEFFXXX',
        ];

        $result = $this->_sanitizer->sanitizeArray($input);

        // Financial data should be masked
        static::assertStringContainsString('*', $result['accountOwner']);
        static::assertStringContainsString('*', $result['iban']);
        static::assertStringContainsString('*', $result['bic']);

        // Amount is numeric but not a sensitive field name, stays unchanged
        static::assertEquals(150.50, $result['codAmount']);
    }

    /**
     * @test
     */
    public function sanitizeArrayHandlesHttpHeaders()
    {
        $input = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic dXNlcjpwYXNz',
                'X-EKP' => '3333333333',
                'DPDHL-User-Authentication-Token' => 'my-secret-token',
            ],
        ];

        $result = $this->_sanitizer->sanitizeArray($input);

        static::assertArrayHasKey('headers', $result);

        // Non-sensitive headers unchanged
        static::assertEquals('application/json', $result['headers']['Content-Type']);
        static::assertEquals('3333333333', $result['headers']['X-EKP']);

        // Sensitive headers masked (field name detection + value masking)
        static::assertStringStartsWith('Ba', $result['headers']['Authorization']); // "Basic..." → "Ba******"
        static::assertStringContainsString('*', $result['headers']['Authorization']);
        // DPDHL token is masked because field name contains sensitive pattern
        static::assertStringStartsWith('my', $result['headers']['DPDHL-User-Authentication-Token']);
        static::assertStringContainsString('*', $result['headers']['DPDHL-User-Authentication-Token']);
    }

    /**
     * @test
     */
    public function sanitizeArrayPreservesStructureForEmptyValues()
    {
        $input = [
            'name' => '',
            'email' => null,
            'phone' => 0,
            'valid' => true,
            'invalid' => false,
        ];

        $result = $this->_sanitizer->sanitizeArray($input);

        // Empty/null sensitive fields - empty string becomes '***' in current implementation
        static::assertEquals('***', $result['name']); // Empty string → '***' (see _maskValue)
        static::assertEquals('', $result['email']); // Null → '' (PHPStan requires string return)

        // Numeric and boolean fields (phone is sensitive field name)
        static::assertEquals('***', $result['phone']); // 0 is numeric, "phone" is sensitive
        static::assertTrue($result['valid']); // Non-sensitive field, keeps original value
        static::assertFalse($result['invalid']); // Non-sensitive field, keeps original value
    }

    /**
     * @test
     */
    public function sanitizeArrayHandlesDeepNesting()
    {
        $input = [
            'level1' => [
                'level2' => [
                    'level3' => [
                        'name' => 'Deep Name',
                        'code' => 'SAFE123',
                    ],
                ],
            ],
        ];

        $result = $this->_sanitizer->sanitizeArray($input);

        // Structure preserved
        static::assertArrayHasKey('level1', $result);
        static::assertArrayHasKey('level2', $result['level1']);
        static::assertArrayHasKey('level3', $result['level1']['level2']);

        // Sensitive field masked at depth
        static::assertStringContainsString('*', $result['level1']['level2']['level3']['name']);

        // Non-sensitive field unchanged
        static::assertEquals('SAFE123', $result['level1']['level2']['level3']['code']);
    }

    /**
     * @test
     */
    public function sanitizeArrayHandlesMixedArrays()
    {
        $input = [
            'items' => [
                ['name' => 'Item 1', 'code' => 'A'],
                ['name' => 'Item 2', 'code' => 'B'],
            ],
            'metadata' => [
                'total' => 2,
                'contactPerson' => 'John Doe',
            ],
        ];

        $result = $this->_sanitizer->sanitizeArray($input);

        // Array of arrays handled
        static::assertCount(2, $result['items']);
        static::assertStringContainsString('*', $result['items'][0]['name']);
        static::assertEquals('A', $result['items'][0]['code']);

        // Mixed structure handled
        static::assertEquals(2, $result['metadata']['total']);
        static::assertStringContainsString('*', $result['metadata']['contactPerson']);
    }

    /**
     * @test
     */
    public function maskValueKeepsFirstTwoChars()
    {
        $input = ['name' => 'Müller'];
        $result = $this->_sanitizer->sanitizeArray($input);

        // Should keep first 2 chars and mask rest (multibyte aware)
        static::assertMatchesRegularExpression('/^.{2}\*+$/', $result['name']); // 2 chars + asterisks
        static::assertNotEquals('Müller', $result['name']);
    }

    /**
     * @test
     */
    public function maskValueHandlesShortStrings()
    {
        $input = [
            'name1' => 'AB',
            'name2' => 'A',
        ];
        $result = $this->_sanitizer->sanitizeArray($input);

        // Short strings get minimal masking
        static::assertEquals('**', $result['name1']);
        static::assertEquals('**', $result['name2']);
    }

    /**
     * @test
     */
    public function sanitizeStringHandlesJsonInHttpMessages()
    {
        // Simulate SDK's FullHttpMessageFormatter output
        $httpMessage = "POST /api/v2/orders HTTP/1.1\r\nHost: api.example.com\r\nContent-Type: application/json\r\nAuthorization: Bearer token123\r\n\r\n{\"name\":\"Max Mustermann\",\"email\":\"max@example.com\",\"phone\":\"123456789\",\"product\":\"V01PAK\"}";

        $result = $this->_sanitizer->sanitizeString($httpMessage);

        // Headers should be sanitized by regex patterns
        static::assertStringContainsString('Authorization: Bearer ***', $result);

        // JSON body should be sanitized
        static::assertStringNotContainsString('Max Mustermann', $result);
        static::assertStringNotContainsString('max@example.com', $result);
        static::assertStringNotContainsString('123456789', $result);

        // Non-sensitive fields should remain
        static::assertStringContainsString('V01PAK', $result);

        // Should still be valid JSON in body
        static::assertMatchesRegularExpression('/\{".*product":"V01PAK".*\}/', $result);
    }

    /**
     * @test
     */
    public function sanitizeStringHandlesComplexNestedJsonInHttpMessages()
    {
        $httpMessage = "POST /api/orders HTTP/1.1\nHost: api.dhl.com\n\n{\"shipper\":{\"name\":\"John Doe\",\"addressStreet\":\"Main St 123\",\"city\":\"Berlin\"},\"consignee\":{\"name\":\"Jane Smith\",\"email\":\"jane@example.com\"},\"product\":\"V01PAK\"}";

        $result = $this->_sanitizer->sanitizeString($httpMessage);

        // Nested personal data should be masked
        static::assertStringNotContainsString('John Doe', $result);
        static::assertStringNotContainsString('Jane Smith', $result);
        static::assertStringNotContainsString('Main St 123', $result);
        static::assertStringNotContainsString('jane@example.com', $result);

        // Non-sensitive data preserved
        static::assertStringContainsString('V01PAK', $result);

        // Structure preserved (shipper/consignee keys remain)
        static::assertStringContainsString('shipper', $result);
        static::assertStringContainsString('consignee', $result);
    }

    /**
     * @test
     */
    public function sanitizeStringHandlesFinancialDataInHttpJson()
    {
        $httpMessage = "POST /api/cod HTTP/1.1\r\nHost: api.dhl.com\r\n\r\n{\"accountHolder\":\"Sebastian Ertner\",\"iban\":\"DE89370400440532013000\",\"bic\":\"COBADEFFXXX\",\"amount\":150.50}";

        $result = $this->_sanitizer->sanitizeString($httpMessage);

        // Financial data should be masked
        static::assertStringNotContainsString('Sebastian Ertner', $result);
        static::assertStringNotContainsString('DE89370400440532013000', $result);
        static::assertStringNotContainsString('COBADEFFXXX', $result);

        // Amount is not sensitive field name (JSON encoding normalizes 150.50 → 150.5)
        static::assertMatchesRegularExpression('/150\.5\d*/', $result);
    }

    /**
     * @test
     */
    public function sanitizeStringPreservesNonJsonHttpMessages()
    {
        $httpMessage = "GET /api/status HTTP/1.1\r\nHost: api.dhl.com\r\nAuthorization: Basic dXNlcjpwYXNz\r\n\r\nNo JSON body here";

        $result = $this->_sanitizer->sanitizeString($httpMessage);

        // Header should be sanitized
        static::assertStringContainsString('Authorization: Basic ***', $result);

        // Non-JSON body should remain unchanged
        static::assertStringContainsString('No JSON body here', $result);
    }

    /**
     * @test
     */
    public function sanitizeStringHandlesInvalidJsonGracefully()
    {
        $httpMessage = "POST /api HTTP/1.1\r\nHost: api.dhl.com\r\n\r\n{invalid json here}";

        $result = $this->_sanitizer->sanitizeString($httpMessage);

        // Should not crash, returns original invalid JSON
        static::assertStringContainsString('{invalid json here}', $result);
    }

    /**
     * @test
     */
    public function sanitizeArrayPreservesExceptionObjects()
    {
        $exception = new \Exception('Test exception');
        $input = [
            'message' => 'Error occurred',
            'exception' => $exception,
            'name' => 'John Doe',
        ];

        $result = $this->_sanitizer->sanitizeArray($input);

        // Exception object must be preserved (PSR-3 standard)
        static::assertSame($exception, $result['exception']);
        static::assertInstanceOf(\Exception::class, $result['exception']);

        // Other sensitive fields should still be masked
        static::assertStringContainsString('*', $result['name']);
        static::assertNotEquals('John Doe', $result['name']);
    }

    /**
     * @test
     */
    public function sanitizeArrayMasksCamelCaseAddressFields()
    {
        // Real SDK field names use camelCase
        $input = [
            'addressStreet' => 'Nonnnenstraße 11d',
            'addressHouse' => '11d',
            'streetName' => 'Main Street',
            'product' => 'V01PAK',
        ];

        $result = $this->_sanitizer->sanitizeArray($input);

        // CamelCase address fields should be masked
        static::assertStringContainsString('*', $result['addressStreet']);
        static::assertNotEquals('Nonnnenstraße 11d', $result['addressStreet']);

        static::assertStringContainsString('*', $result['addressHouse']);
        static::assertNotEquals('11d', $result['addressHouse']);

        static::assertStringContainsString('*', $result['streetName']);
        static::assertNotEquals('Main Street', $result['streetName']);

        // Non-sensitive field unchanged
        static::assertEquals('V01PAK', $result['product']);
    }

    /**
     * @test
     */
    public function sanitizeArrayHandlesObjectWithToArray()
    {
        // Create Varien_Object which has toArray() method
        $obj = new Varien_Object([
            'name' => 'John Doe',
            'product' => 'V01PAK',
        ]);

        $input = [
            'data' => $obj,
        ];

        $result = $this->_sanitizer->sanitizeArray($input);

        // Object should be converted via toArray() and sanitized
        static::assertIsArray($result['data']);
        static::assertArrayHasKey('name', $result['data']);
        static::assertStringContainsString('*', $result['data']['name']);
        static::assertEquals('V01PAK', $result['data']['product']);
    }

    /**
     * @test
     */
    public function sanitizeArrayHandlesObjectWithToString()
    {
        // Create object with __toString() but no toArray()
        $obj = new class {
            public function __toString()
            {
                return 'Contact: max@example.com';
            }
        };

        $input = [
            'message' => $obj,
        ];

        $result = $this->_sanitizer->sanitizeArray($input);

        // Object should be converted to string and sanitized
        static::assertIsString($result['message']);
        static::assertStringContainsString('***@example.com', $result['message']);
        static::assertStringNotContainsString('max', $result['message']);
    }

    /**
     * @test
     */
    public function sanitizeArrayHandlesPlainObject()
    {
        // Create plain object without toArray or __toString
        $obj = new stdClass();
        $obj->name = 'Test';

        $input = [
            'obj' => $obj,
        ];

        $result = $this->_sanitizer->sanitizeArray($input);

        // Plain object falls back to class name
        static::assertStringContainsString('stdClass', $result['obj']);
        static::assertStringContainsString('(object)', $result['obj']);
    }

    /**
     * @test
     */
    public function sanitizeStringReturnsNonStringUnchanged()
    {
        static::assertEquals(123, $this->_sanitizer->sanitizeString(123));
        static::assertEquals(12.34, $this->_sanitizer->sanitizeString(12.34));
        static::assertTrue($this->_sanitizer->sanitizeString(true));
        static::assertNull($this->_sanitizer->sanitizeString(null));
        static::assertEquals(['a' => 1], $this->_sanitizer->sanitizeString(['a' => 1]));
    }

    /**
     * @test
     */
    public function sanitizeArrayMasksBooleanSensitiveFields()
    {
        // Booleans in sensitive fields get string conversion
        $input = [
            'name' => true,  // sensitive field with boolean
            'active' => true, // non-sensitive field
        ];

        $result = $this->_sanitizer->sanitizeArray($input);

        // Boolean in sensitive field gets masked as string 'true'/'false'
        static::assertEquals('true', $result['name']);
        static::assertTrue($result['active']);
    }
}
