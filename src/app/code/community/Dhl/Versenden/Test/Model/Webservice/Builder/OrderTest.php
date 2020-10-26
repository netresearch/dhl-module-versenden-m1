<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Test_Model_Webservice_Builder_OrderTest
    extends EcomDev_PHPUnit_Test_Case
{
    protected function getBuilders()
    {
        $shipperBuilder = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Builder_Shipper::class)
            ->disableOriginalConstructor()
            ->getMock();
        $receiverBuilder = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Builder_Receiver::class)
            ->disableOriginalConstructor()
            ->getMock();
        $serviceBuilder = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Builder_Service::class)
            ->disableOriginalConstructor()
            ->getMock();
        $packageBuilder = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Builder_Package::class)
            ->disableOriginalConstructor()
            ->getMock();
        $customsBuilder = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Builder_Customs::class)
            ->disableOriginalConstructor()
            ->getMock();
        $settingsBuilder = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Builder_Settings::class)
            ->disableOriginalConstructor()
            ->getMock();

        return array(
            'shipper_builder' => $shipperBuilder,
            'receiver_builder' => $receiverBuilder,
            'service_builder' => $serviceBuilder,
            'package_builder' => $packageBuilder,
            'customs_builder' => $customsBuilder,
            'settings_builder' => $settingsBuilder,
        );
    }

    /**
     * @test
     * @expectedException Mage_Core_Exception
     */
    public function constructorArgShipperBuilderMissing()
    {
        $args = $this->getBuilders();
        unset($args['shipper_builder']);

        new Dhl_Versenden_Model_Webservice_Builder_Order($args);
    }

    /**
     * @test
     * @expectedException Mage_Core_Exception
     */
    public function constructorArgShipperBuilderWrongType()
    {
        $args = $this->getBuilders();
        $args['shipper_builder'] = new stdClass();

        new Dhl_Versenden_Model_Webservice_Builder_Order($args);
    }

    /**
     * @test
     * @expectedException Mage_Core_Exception
     */
    public function constructorArgReceiverBuilderMissing()
    {
        $args = $this->getBuilders();
        unset($args['receiver_builder']);

        new Dhl_Versenden_Model_Webservice_Builder_Order($args);
    }

    /**
     * @test
     * @expectedException Mage_Core_Exception
     */
    public function constructorArgReceiverBuilderWrongType()
    {
        $args = $this->getBuilders();
        $args['receiver_builder'] = new stdClass();

        new Dhl_Versenden_Model_Webservice_Builder_Order($args);
    }

    /**
     * @test
     * @expectedException Mage_Core_Exception
     */
    public function constructorArgServiceBuilderMissing()
    {
        $args = $this->getBuilders();
        unset($args['service_builder']);

        new Dhl_Versenden_Model_Webservice_Builder_Order($args);
    }

    /**
     * @test
     * @expectedException Mage_Core_Exception
     */
    public function constructorArgServiceBuilderWrongType()
    {
        $args = $this->getBuilders();
        $args['service_builder'] = new stdClass();

        new Dhl_Versenden_Model_Webservice_Builder_Order($args);
    }

    /**
     * @test
     * @expectedException Mage_Core_Exception
     */
    public function constructorArgPackageBuilderMissing()
    {
        $args = $this->getBuilders();
        unset($args['package_builder']);

        new Dhl_Versenden_Model_Webservice_Builder_Order($args);
    }

    /**
     * @test
     * @expectedException Mage_Core_Exception
     */
    public function constructorArgPackageBuilderWrongType()
    {
        $args = $this->getBuilders();
        $args['package_builder'] = new stdClass();

        new Dhl_Versenden_Model_Webservice_Builder_Order($args);
    }

    /**
     * @test
     * @expectedException Mage_Core_Exception
     */
    public function constructorArgSettingsBuilderMissing()
    {
        $args = $this->getBuilders();
        unset($args['settings_builder']);

        new Dhl_Versenden_Model_Webservice_Builder_Order($args);
    }

    /**
     * @test
     * @expectedException Mage_Core_Exception
     */
    public function constructorArgSettingsBuilderWrongType()
    {
        $args = $this->getBuilders();
        $args['settings_builder'] = new stdClass();

        new Dhl_Versenden_Model_Webservice_Builder_Order($args);
    }
}
