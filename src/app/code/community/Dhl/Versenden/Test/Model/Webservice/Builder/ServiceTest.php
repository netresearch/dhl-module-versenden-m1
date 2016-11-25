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
use \Dhl\Versenden\Bcs\Api\Shipment\Service;
/**
 * Dhl_Versenden_Test_Model_Webservice_Builder_ServiceTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Test_Model_Webservice_Builder_ServiceTest
    extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     * @expectedException Mage_Core_Exception
     */
    public function constructorArgShipperConfigMissing()
    {
        new Dhl_Versenden_Model_Webservice_Builder_Service(array(
            'shipment_config' => Mage::getModel('dhl_versenden/config_shipment'),
        ));
    }

    /**
     * @test
     * @expectedException Mage_Core_Exception
     */
    public function constructorArgShipperConfigWrongType()
    {
        new Dhl_Versenden_Model_Webservice_Builder_Service(array(
            'shipper_config' => new stdClass(),
            'shipment_config' => Mage::getModel('dhl_versenden/config_shipment'),
        ));
    }

    /**
     * @test
     * @expectedException Mage_Core_Exception
     */
    public function constructorArgShipmentConfigMissing()
    {
        new Dhl_Versenden_Model_Webservice_Builder_Service(array(
            'shipper_config' => Mage::getModel('dhl_versenden/config_shipper'),
        ));
    }

    /**
     * @test
     * @expectedException Mage_Core_Exception
     */
    public function constructorArgShipmentConfigWrongType()
    {
        new Dhl_Versenden_Model_Webservice_Builder_Service(array(
            'shipper_config' => Mage::getModel('dhl_versenden/config_shipper'),
            'shipment_config' => new stdClass(),
        ));
    }

    /**
     * @test
     * @loadFixture Model_ShipperConfigTest
     * @loadFixture Model_ShipmentConfigTest
     */
    public function getServiceSelection()
    {
        $builder = new Dhl_Versenden_Model_Webservice_Builder_Service(array(
            'shipper_config' => Mage::getModel('dhl_versenden/config_shipper'),
            'shipment_config' => Mage::getModel('dhl_versenden/config_shipment'),
        ));

        $orderAmount        = '19.8600';
        $preferredNeighbour = 'Alf';
        $preferredLocation  = 'Melmac';

        $selectedServices = array(
            Service\BulkyGoods::CODE => '1',
            Service\Insurance::CODE => '1',
            Service\PreferredNeighbour::CODE => '1',
        );

        $serviceDetails = array(
            Service\PreferredNeighbour::CODE => $preferredNeighbour,
            Service\PreferredLocation::CODE => $preferredLocation,
        );

        $serviceInfo = array(
            'shipment_service' => $selectedServices,
            'service_setting' => $serviceDetails,
        );

        $payment = new Mage_Sales_Model_Order_Payment();
        $payment->setMethod('cashondelivery');
        $order = new Mage_Sales_Model_Order();
        $order->setStoreId(1);
        $order->setBaseGrandTotal($orderAmount);
        $order->setPayment($payment);

        $selection = $builder->getServiceSelection($order, $serviceInfo);
        $this->assertInstanceOf(
            \Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder\ServiceSelection::class,
            $selection
        );

        $this->assertTrue($selection->isBulkyGoods());
        $this->assertEquals($orderAmount, $selection->getInsurance());
        $this->assertEquals($orderAmount, $selection->getCod());
        $this->assertSame($preferredNeighbour, $selection->getPreferredNeighbour());
        $this->assertFalse($selection->getPreferredLocation());
    }
}
