<?php
/**
 * Dhl Versenden
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer versions in the future.
 *
 * PHP version 5
 *
 * @category  Dhl
 * @package   Dhl_Versenden
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */

/**
 * Dhl_Versenden_Test_Model_Webservice_Builder_OrderTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
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
        $settingsBuilder = $this->getMockBuilder(Dhl_Versenden_Model_Webservice_Builder_Settings::class)
            ->disableOriginalConstructor()
            ->getMock();

        return array(
            'shipper_builder' => $shipperBuilder,
            'receiver_builder' => $receiverBuilder,
            'service_builder' => $serviceBuilder,
            'package_builder' => $packageBuilder,
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
