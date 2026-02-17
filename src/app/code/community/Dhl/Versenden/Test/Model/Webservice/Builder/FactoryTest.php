<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Test_Model_Webservice_Builder_FactoryTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     * @loadFixture Model_ShipperConfigTest
     * @loadFixture Model_ShipmentConfigTest
     */
    public function createOrderBuilderReturnsOrderBuilder()
    {
        $factory = Mage::getModel('dhl_versenden/webservice_builder_factory');

        $orderBuilder = $factory->createOrderBuilder();

        static::assertInstanceOf(
            Dhl_Versenden_Model_Webservice_Builder_Order::class,
            $orderBuilder
        );
    }

    /**
     * @test
     * @loadFixture Model_ShipperConfigTest
     * @loadFixture Model_ShipmentConfigTest
     */
    public function createOrderBuilderAcceptsCustomMinWeight()
    {
        $factory = Mage::getModel('dhl_versenden/webservice_builder_factory');

        // Should not throw exception with custom min weight
        $orderBuilder = $factory->createOrderBuilder(0.5);

        static::assertInstanceOf(
            Dhl_Versenden_Model_Webservice_Builder_Order::class,
            $orderBuilder
        );
    }

    /**
     * @test
     * @loadFixture Model_ShipmentConfigTest
     */
    public function createSettingsBuilderReturnsSettingsBuilder()
    {
        $factory = Mage::getModel('dhl_versenden/webservice_builder_factory');

        $settingsBuilder = $factory->createSettingsBuilder();

        static::assertInstanceOf(
            Dhl_Versenden_Model_Webservice_Builder_Settings::class,
            $settingsBuilder
        );
    }
}
