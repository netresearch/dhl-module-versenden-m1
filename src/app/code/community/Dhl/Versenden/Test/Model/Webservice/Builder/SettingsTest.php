<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Test_Model_Webservice_Builder_SettingsTest
    extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     * @expectedException Mage_Core_Exception
     */
    public function constructorArgShipmentConfigMissing()
    {
        new Dhl_Versenden_Model_Webservice_Builder_Settings(array(
        ));
    }

    /**
     * @test
     * @expectedException Mage_Core_Exception
     */
    public function constructorArgShipmentConfigWrongType()
    {
        new Dhl_Versenden_Model_Webservice_Builder_Settings(array(
            'config' => new stdClass(),
        ));
    }

    /**
     * @test
     * @loadFixture Model_ShipmentConfigTest
     */
    public function getShipper()
    {
        $builder = new Dhl_Versenden_Model_Webservice_Builder_Settings(array(
            'config' => Mage::getModel('dhl_versenden/config_shipment'),
        ));
        $settings = $builder->getSettings(2);

        $this->assertInstanceOf(\Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder\GlobalSettings::class, $settings);
        $this->assertEquals("G", $settings->getUnitOfMeasure());
    }
}
