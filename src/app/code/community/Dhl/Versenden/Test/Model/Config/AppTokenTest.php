<?php

/**
 * See LICENSE.md for license details.
 */

/**
 * App Token Configuration Test
 *
 * Tests for the DHL REST API app_token configuration value.
 * This token is used for REST API authentication and should be
 * available in the default carrier configuration.
 *
 * @package Dhl_Versenden
 */
class Dhl_Versenden_Test_Model_Config_AppTokenTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     */
    public function appTokenExistsInDefaultConfig()
    {
        $configPath = 'carriers/dhlversenden/app_token';
        $appToken = (string) Mage::getConfig()->getNode('default/' . $configPath);

        static::assertNotEmpty(
            $appToken,
            'App token must exist in default config at carriers/dhlversenden/app_token',
        );
    }

    /**
     * @test
     */
    public function appTokenIsRetrievableViaStoreConfig()
    {
        $configPath = 'carriers/dhlversenden/app_token';
        $appToken = Mage::getStoreConfig($configPath);

        static::assertNotEmpty(
            $appToken,
            'App token must be retrievable via Mage::getStoreConfig()',
        );

        static::assertIsString(
            $appToken,
            'App token must be a string value',
        );
    }

    /**
     * @test
     */
    public function appTokenHasExpectedPlaceholderValue()
    {
        $configPath = 'carriers/dhlversenden/app_token';
        $appToken = Mage::getStoreConfig($configPath);

        $expectedToken = 'M1_SHIPPING_1';

        static::assertEquals(
            $expectedToken,
            $appToken,
            'App token must have the expected value from DHL Parcel DE API',
        );
    }

    /**
     * @test
     */
    public function moduleVersionIsUpdatedToTwoZeroZero()
    {
        $version = (string) Mage::getConfig()->getNode('modules/Dhl_Versenden/version');

        static::assertEquals(
            '2.0.0',
            $version,
            'Module version must be updated to 2.0.0 for REST API support',
        );
    }
}
