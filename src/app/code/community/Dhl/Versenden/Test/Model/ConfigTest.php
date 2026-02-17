<?php

/**
 * See LICENSE.md for license details.
 */


class Dhl_Versenden_Test_Model_ConfigTest extends EcomDev_PHPUnit_Test_Case
{
    protected function disableSandboxMode()
    {
        $path = sprintf(
            '%s/%s/%s',
            Dhl_Versenden_Model_Config::CONFIG_SECTION,
            Dhl_Versenden_Model_Config::CONFIG_GROUP,
            Dhl_Versenden_Model_Config::CONFIG_XML_PATH_SANDBOX_MODE,
        );
        Mage::app()->getStore()->setConfig($path, '0');
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function isAutoloadEnabled()
    {
        $config = new Dhl_Versenden_Model_Config();
        static::assertFalse($config->isAutoloadEnabled());
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getTitle()
    {
        $config = new Dhl_Versenden_Model_Config();
        static::assertEquals('foo', $config->getTitle());
        static::assertEquals('bar', $config->getTitle('store_one'));
        static::assertEquals('baz', $config->getTitle('store_two'));
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function isActive()
    {
        $config = new Dhl_Versenden_Model_Config();
        static::assertTrue($config->isActive());
        static::assertFalse($config->isActive('store_one'));
        static::assertTrue($config->isActive('store_two'));
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function isSandboxModeEnabled()
    {
        $config = new Dhl_Versenden_Model_Config();

        // sandbox
        static::assertTrue($config->isSandboxModeEnabled());

        // production
        $this->disableSandboxMode();
        static::assertFalse($config->isSandboxModeEnabled());
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function isLoggingEnabled()
    {
        $config = new Dhl_Versenden_Model_Config();
        static::assertTrue($config->isLoggingEnabled());
        static::assertTrue($config->isLoggingEnabled(Zend_Log::DEBUG));
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getWebserviceCredentials()
    {
        $config = new Dhl_Versenden_Model_Config();

        // sandbox
        static::assertEquals('uBar', $config->getWebserviceAuthUsername());
        static::assertEquals('pBar', $config->getWebserviceAuthPassword());

        // production
        $this->disableSandboxMode();
        static::assertEquals('uFoo', $config->getWebserviceAuthUsername());
        static::assertEquals('pFoo', $config->getWebserviceAuthPassword());
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function isAutoCreateNotifyCustomer()
    {
        $config = new Dhl_Versenden_Model_Config();
        static::assertTrue($config->isAutoCreateNotifyCustomer());
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function isSendReceiverPhone()
    {
        $config = new Dhl_Versenden_Model_Config();
        static::assertTrue($config->isSendReceiverPhone());
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getShipperCountry()
    {
        $config = new Dhl_Versenden_Model_Config();
        // Default store has US, store_one has DE, store_two has AT
        static::assertEquals('US', $config->getShipperCountry());
        static::assertEquals('DE', $config->getShipperCountry('store_one'));
        static::assertEquals('AT', $config->getShipperCountry('store_two'));
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function isShipmentAutoCreateEnabled()
    {
        $config = new Dhl_Versenden_Model_Config();
        static::assertTrue($config->isShipmentAutoCreateEnabled());
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getAutoCreateOrderStatus()
    {
        $config = new Dhl_Versenden_Model_Config();
        $statuses = $config->getAutoCreateOrderStatus();
        static::assertIsArray($statuses);
        static::assertContains('processing', $statuses);
        static::assertContains('pending', $statuses);
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getAutoCreateShippingProduct()
    {
        $config = new Dhl_Versenden_Model_Config();
        $product = $config->getAutoCreateShippingProduct();
        static::assertEquals('V01PAK', $product);
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getExcludedDropOffDays()
    {
        $config = new Dhl_Versenden_Model_Config();
        $days = $config->getExcludedDropOffDays();
        static::assertEquals('0,6', $days);
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getParcelManagementEndpointSandbox()
    {
        $config = new Dhl_Versenden_Model_Config();
        // In sandbox mode (default in fixture)
        $endpoint = $config->getParcelManagementEndpoint();
        static::assertEquals('https://api-sandbox.dhl.com/parcelmanagement', $endpoint);
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getParcelManagementEndpointProduction()
    {
        $this->disableSandboxMode();
        $config = new Dhl_Versenden_Model_Config();
        $endpoint = $config->getParcelManagementEndpoint();
        static::assertEquals('https://api.dhl.com/parcelmanagement', $endpoint);
    }

}
