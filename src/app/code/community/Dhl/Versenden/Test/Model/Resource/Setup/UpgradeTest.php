<?php

/**
 * See LICENSE.md for license details.
 */

/**
 * Test for upgrade script behavior during migration to 2.0.0
 *
 * The upgrade script removes obsolete SOAP endpoint configuration but KEEPS
 * authentication credentials since they are reused for the REST API.
 *
 * @category  Dhl
 * @package   Dhl_Versenden
 */
class Dhl_Versenden_Test_Model_Resource_Setup_UpgradeTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @var Mage_Core_Model_Resource_Setup
     */
    protected $installer;

    /**
     * Config paths modified during tests that need cleanup
     *
     * @var string[]
     */
    private $configPathsToClean = [
        'carriers/dhlversenden/sandbox_endpoint',
        'carriers/dhlversenden/production_endpoint',
        'carriers/dhlversenden/webservice_auth_username',
        'carriers/dhlversenden/webservice_auth_password',
        'carriers/dhlversenden/account_number',
    ];

    /**
     * Set up test environment
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->installer = Mage::getResourceModel('core/setup', 'core_setup');
    }

    /**
     * Clean up database modifications to prevent fixture pollution
     *
     * @return void
     */
    protected function tearDown(): void
    {
        $connection = $this->installer->getConnection();
        $table = $this->installer->getTable('core/config_data');

        foreach ($this->configPathsToClean as $path) {
            $connection->delete($table, [
                'path = ?' => $path,
                'scope = ?' => 'default',
                'scope_id = ?' => 0,
            ]);
        }

        parent::tearDown();
    }

    /**
     * Test that upgrade script removes obsolete SOAP endpoint config but keeps credentials
     *
     * @test
     */
    public function upgradeTo200RemovesOnlyEndpointConfig()
    {
        // Arrange: Set up test config values
        $endpointPaths = [
            'carriers/dhlversenden/sandbox_endpoint',
            'carriers/dhlversenden/production_endpoint',
        ];
        $credentialPaths = [
            'carriers/dhlversenden/webservice_auth_username',
            'carriers/dhlversenden/webservice_auth_password',
        ];

        foreach (array_merge($endpointPaths, $credentialPaths) as $path) {
            $this->installer->setConfigData($path, 'test_value');
        }

        // Act: Run the upgrade script
        $upgradeScriptPath = dirname(__DIR__) . '/../../../sql/dhl_versenden_setup/upgrade-1.14.0-2.0.0.php';
        static::assertFileExists($upgradeScriptPath, 'Upgrade script file should exist');

        $installer = $this->installer;
        include $upgradeScriptPath;

        // Assert: Endpoints removed, credentials kept
        $connection = $this->installer->getConnection();

        foreach ($endpointPaths as $path) {
            $value = $this->getConfigValue($connection, $path);
            static::assertFalse($value, "Endpoint config {$path} should be removed");
        }

        foreach ($credentialPaths as $path) {
            $value = $this->getConfigValue($connection, $path);
            static::assertEquals('test_value', $value, "Credential config {$path} should be kept for REST API");
        }
    }

    /**
     * Test that upgrade script is idempotent (safe to run multiple times)
     *
     * @test
     */
    public function upgradeTo200IsIdempotent()
    {
        // Arrange
        $endpointPath = 'carriers/dhlversenden/sandbox_endpoint';
        $credentialPath = 'carriers/dhlversenden/webservice_auth_username';

        $this->installer->setConfigData($endpointPath, 'test_endpoint');
        $this->installer->setConfigData($credentialPath, 'test_username');

        // Act: Run upgrade script twice
        $upgradeScriptPath = dirname(__DIR__) . '/../../../sql/dhl_versenden_setup/upgrade-1.14.0-2.0.0.php';
        $installer = $this->installer;

        include $upgradeScriptPath;
        include $upgradeScriptPath;

        // Assert: No exceptions, endpoints removed, credentials kept
        $connection = $this->installer->getConnection();

        $endpointValue = $this->getConfigValue($connection, $endpointPath);
        $credentialValue = $this->getConfigValue($connection, $credentialPath);

        static::assertFalse($endpointValue, 'Endpoint should remain deleted after second run');
        static::assertEquals('test_username', $credentialValue, 'Credentials should remain after second run');
    }

    /**
     * Test that upgrade script only removes endpoint fields, not credentials or other config
     *
     * @test
     */
    public function upgradeTo200OnlyRemovesEndpointFields()
    {
        // Arrange
        $endpointPath = 'carriers/dhlversenden/sandbox_endpoint';
        $credentialPath = 'carriers/dhlversenden/webservice_auth_username';
        $otherPath = 'carriers/dhlversenden/account_number';

        $this->installer->setConfigData($endpointPath, 'test_endpoint');
        $this->installer->setConfigData($credentialPath, 'test_username');
        $this->installer->setConfigData($otherPath, 'valid_account_number');

        // Act
        $upgradeScriptPath = dirname(__DIR__) . '/../../../sql/dhl_versenden_setup/upgrade-1.14.0-2.0.0.php';
        $installer = $this->installer;
        include $upgradeScriptPath;

        // Assert
        $connection = $this->installer->getConnection();

        $endpointValue = $this->getConfigValue($connection, $endpointPath);
        $credentialValue = $this->getConfigValue($connection, $credentialPath);
        $otherValue = $this->getConfigValue($connection, $otherPath);

        static::assertFalse($endpointValue, 'Endpoint config should be removed');
        static::assertEquals('test_username', $credentialValue, 'Credentials should be kept for REST API');
        static::assertEquals('valid_account_number', $otherValue, 'Other config should remain');
    }

    /**
     * Helper to get config value from database
     *
     * @param Varien_Db_Adapter_Interface $connection
     * @param string $path
     * @return string|false
     */
    private function getConfigValue($connection, $path)
    {
        $select = $connection->select()
            ->from($this->installer->getTable('core/config_data'), 'value')
            ->where('path = ?', $path)
            ->where('scope = ?', 'default')
            ->where('scope_id = ?', 0);

        return $connection->fetchOne($select);
    }
}
