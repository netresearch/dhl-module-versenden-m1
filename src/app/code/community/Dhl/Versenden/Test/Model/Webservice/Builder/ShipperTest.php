<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Test_Model_Webservice_Builder_ShipperTest
    extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     * @expectedException Mage_Core_Exception
     */
    public function constructorArgShipperConfigMissing()
    {
        new Dhl_Versenden_Model_Webservice_Builder_Shipper(array(
        ));
    }

    /**
     * @test
     * @expectedException Mage_Core_Exception
     */
    public function constructorArgShipperConfigWrongType()
    {
        new Dhl_Versenden_Model_Webservice_Builder_Shipper(array(
            'config' => new stdClass(),
        ));
    }

    /**
     * @test
     * @loadFixture Model_ShipperConfigTest
     */
    public function getShipper()
    {
        $builder = new Dhl_Versenden_Model_Webservice_Builder_Shipper(array(
            'config' => Mage::getModel('dhl_versenden/config_shipper'),
        ));
        $shipper = $builder->getShipper(2);

        $this->assertInstanceOf(\Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder\Shipper::class, $shipper);
        $this->assertEquals("Bar Name", $shipper->getContact()->getName1());
    }
}
