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
            Dhl_Versenden_Model_Config::CONFIG_XML_PATH_SANDBOX_MODE
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
        $this->assertFalse($config->isAutoloadEnabled());
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getTitle()
    {
        $config = new Dhl_Versenden_Model_Config();
        $this->assertEquals('foo', $config->getTitle());
        $this->assertEquals('bar', $config->getTitle('store_one'));
        $this->assertEquals('baz', $config->getTitle('store_two'));
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function isActive()
    {
        $config = new Dhl_Versenden_Model_Config();
        $this->assertTrue($config->isActive());
        $this->assertFalse($config->isActive('store_one'));
        $this->assertTrue($config->isActive('store_two'));
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function isSandboxModeEnabled()
    {
        $config = new Dhl_Versenden_Model_Config();

        // sandbox
        $this->assertTrue($config->isSandboxModeEnabled());

        // production
        $this->disableSandboxMode();
        $this->assertFalse($config->isSandboxModeEnabled());
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function isLoggingEnabled()
    {
        $config = new Dhl_Versenden_Model_Config();
        $this->assertTrue($config->isLoggingEnabled());
        $this->assertTrue($config->isLoggingEnabled(Zend_Log::DEBUG));
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getWebserviceCredentials()
    {
        $config = new Dhl_Versenden_Model_Config();

        // sandbox
        $this->assertEquals('uBar', $config->getWebserviceAuthUsername());
        $this->assertEquals('pBar', $config->getWebserviceAuthPassword());

        // production
        $this->disableSandboxMode();
        $this->assertEquals('uFoo', $config->getWebserviceAuthUsername());
        $this->assertEquals('pFoo', $config->getWebserviceAuthPassword());
    }

    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function getWebserviceEndpoint()
    {
        $config = new Dhl_Versenden_Model_Config();

        // sandbox
        $this->assertEquals('sandbox endpoint', $config->getEndpoint());

        // production
        $this->disableSandboxMode();
        $this->assertNull($config->getEndpoint());
    }
}
