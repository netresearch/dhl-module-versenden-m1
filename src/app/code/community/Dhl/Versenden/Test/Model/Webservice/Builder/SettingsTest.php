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
 * Dhl_Versenden_Test_Model_Webservice_Builder_SettingsTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
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

        $this->assertInstanceOf(\Dhl\Versenden\Webservice\RequestData\ShipmentOrder\GlobalSettings::class, $settings);
        $this->assertEquals("KG", $settings->getUnitOfMeasure());
    }
}
