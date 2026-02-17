<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Test_Model_Webservice_Builder_SettingsTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     */
    public function constructorArgShipmentConfigMissing()
    {
        $this->expectException(Mage_Core_Exception::class);

        new Dhl_Versenden_Model_Webservice_Builder_Settings([
        ]);
    }

    /**
     * @test
     */
    public function constructorArgShipmentConfigWrongType()
    {
        $this->expectException(Mage_Core_Exception::class);

        new Dhl_Versenden_Model_Webservice_Builder_Settings([
            'config' => new stdClass(),
        ]);
    }

    /**
     * @test
     * @loadFixture Model_ShipmentConfigTest
     */
    public function getShipper()
    {
        $builder = new Dhl_Versenden_Model_Webservice_Builder_Settings([
            'config' => Mage::getModel('dhl_versenden/config_shipment'),
        ]);
        $settings = $builder->getSettings(2);

        static::assertInstanceOf(\Dhl\Versenden\ParcelDe\Config\Data\GlobalSettings::class, $settings);
        static::assertEquals('G', $settings->getUnitOfMeasure());
    }

    /**
     * @test
     * @loadFixture Model_ShipmentConfigTest
     */
    public function buildReturnsOrderConfiguration()
    {
        $builder = new Dhl_Versenden_Model_Webservice_Builder_Settings([
            'config' => Mage::getModel('dhl_versenden/config_shipment'),
        ]);

        $orderConfig = $builder->build(2);

        static::assertInstanceOf(\Dhl\Sdk\ParcelDe\Shipping\Api\Data\OrderConfigurationInterface::class, $orderConfig);
        // Settings builder should prepare order configuration for label format
        static::assertNotNull($orderConfig);
    }
}
