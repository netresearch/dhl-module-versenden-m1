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
 * Dhl_Versenden_Test_Model_Webservice_Builder_ShipperTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
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

        $this->assertInstanceOf(\Dhl\Versenden\Webservice\RequestData\ShipmentOrder\Shipper::class, $shipper);
        $this->assertEquals("Bar Name", $shipper->getContact()->getName1());
    }
}
