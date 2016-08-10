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
use \Dhl\Versenden\Webservice\RequestData\ShipmentOrder;
/**
 * Dhl_Versenden_Test_Helper_WebserviceTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Test_Helper_WebserviceTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     */
    public function getPackageWeight()
    {
        $helper = new Dhl_Versenden_Helper_Webservice();

        $weightInKg = 1.6;
        $weightInG  = 1600;

        $settings = $this->getMockBuilder(ShipmentOrder\GlobalSettings::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getUnitOfMeasure'))
            ->getMock();
        $settings
            ->expects($this->exactly(2))
            ->method('getUnitOfMeasure')
            ->willReturnOnConsecutiveCalls('KG', 'G');

        $this->assertEquals($weightInKg, $helper->getPackageWeight($settings, $weightInKg));
        $this->assertEquals($weightInG, $helper->getPackageWeight($settings, $weightInG));
    }

    /**
     * @test
     */
    public function getPackageMinWeight()
    {
        $helper = new Dhl_Versenden_Helper_Webservice();

        $weightInKg = 0.09;
        $weightInG  = 90;
        $minWeightInKg  = Dhl_Versenden_Model_Shipping_Carrier_Versenden::PACKAGE_MIN_WEIGHT;
        $minWeightInG  = 1000*Dhl_Versenden_Model_Shipping_Carrier_Versenden::PACKAGE_MIN_WEIGHT;

        $settings = $this->getMockBuilder(ShipmentOrder\GlobalSettings::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getUnitOfMeasure'))
            ->getMock();
        $settings
            ->expects($this->exactly(2))
            ->method('getUnitOfMeasure')
            ->willReturnOnConsecutiveCalls('KG', 'G');

        $this->assertEquals($minWeightInKg, $helper->getPackageWeight($settings, $weightInKg));
        $this->assertEquals($minWeightInG, $helper->getPackageWeight($settings, $weightInG));
    }

    /**
     * @test
     */
    public function serviceSelectionToServiceSettings()
    {
        $helper = new Dhl_Versenden_Helper_Webservice();

        $insuranceType      = '∞';
        $preferredNeighbour = 'Alf';
        $preferredLocation  = 'Melmac';

        $selectedServices = array(
            'bulkyGoods' => '1',
            'insurance' => '1',
            'preferredNeighbour' => '1',
        );

        $serviceDetails = array(
            'insurance' => $insuranceType,
            'preferredNeighbour' => $preferredNeighbour,
            'preferredLocation' => $preferredLocation,
        );

        $selection = $helper->serviceSelectionToServiceSettings($selectedServices, $serviceDetails);
        $this->assertInstanceOf(ShipmentOrder\ServiceSelection::class, $selection);

        $this->assertTrue($selection->isBulkyGoods());
        $this->assertSame($insuranceType, $selection->getInsurance());
        $this->assertSame($preferredNeighbour, $selection->getPreferredNeighbour());
        $this->assertFalse($selection->getPreferredLocation());
    }

    /**
     * @test
     */
    public function addStatusHistoryComment()
    {
        $helper = new Dhl_Versenden_Helper_Webservice();

        $history = $this->getMockBuilder(Mage_Sales_Model_Resource_Order_Status_History_Collection::class)
            ->setMethods(array('save'))
            ->getMock();

        $order = $this->getMockBuilder(Mage_Sales_Model_Order::class)
            ->setMethods(array('getStatusHistoryCollection'))
            ->getMock();
        $order
            ->expects($this->exactly(2))
            ->method('getStatusHistoryCollection')
            ->willReturn($history);

        $comment = 'status comment';

        /** @var Mage_Sales_Model_Order $order */
        /** @var Mage_Sales_Model_Resource_Order_Status_History_Collection $history */

        $this->assertCount(0, $history);
        $helper->addStatusHistoryInfo($order, $comment);
        $this->assertCount(1, $history);
        $helper->addStatusHistoryError($order, $comment);
        $this->assertCount(2, $history);

        /** @var Mage_Sales_Model_Order_Status_History $item */
        foreach ($history as $item) {
            $this->assertStringEndsWith($comment, $item->getComment());
        }
    }

    /**
     * @test
     */
    public function shippingAddressToReceiver()
    {
        $helper = new Dhl_Versenden_Helper_Webservice();

        $firstName = 'Foo';
        $lastName = 'Bar';
        $name = "$firstName $lastName";
        $company = 'Foo Inc.';
        $streetName = 'Xx';
        $streetNumber = '111';
        $streetFull = "$streetName $streetNumber";
        $postCode = '12345';
        $city = 'Foo';
        $country = 'DE';
        $telephone = '54321';
        $email = 'a@b.c';

        $address = new Mage_Sales_Model_Quote_Address();
        $address->setFirstname($firstName);
        $address->setLastname($lastName);
        $address->setCompany($company);
        $address->setStreetFull($streetFull);
        $address->setPostcode($postCode);
        $address->setCity($city);
        $address->setCountry($country);
        $address->setTelephone($telephone);
        $address->setEmail($email);

        $receiver = $helper->shippingAddressToReceiver($address);
        $this->assertSame($name, $receiver->getName1());
        $this->assertSame($company, $receiver->getName2());
        $this->assertSame($streetName, $receiver->getStreetName());
        $this->assertSame($streetNumber, $receiver->getStreetNumber());
        $this->assertSame($postCode, $receiver->getZip());
        $this->assertSame($city, $receiver->getCity());
        $this->assertSame($country, $receiver->getCountryISOCode());
        $this->assertSame($telephone, $receiver->getPhone());
        $this->assertSame($email, $receiver->getEmail());
        $this->assertNull($receiver->getPackstation());
        $this->assertNull($receiver->getPostfiliale());
        $this->assertNull($receiver->getParcelShop());


        $streetName = 'Packstation';
        $streetNumber = '111';
        $streetFull = "$streetName $streetNumber";
        $postNumber = '123456';

        $address->setStreetFull($streetFull);
        $address->setCompany($postNumber);

        $receiver = $helper->shippingAddressToReceiver($address);

        $this->assertNotNull($receiver->getPackstation());
        $this->assertSame($streetNumber, $receiver->getPackstation()->getPackstationNumber());
        $this->assertSame($postNumber, $receiver->getPackstation()->getPostNumber());
        $this->assertSame($postCode, $receiver->getPackstation()->getZip());
        $this->assertSame($city, $receiver->getPackstation()->getCity());
        $this->assertSame($country, $receiver->getPackstation()->getCountryISOCode());
        $this->assertNull($receiver->getPostfiliale());
        $this->assertNull($receiver->getParcelShop());


        $streetName = 'Postfiliale';
        $streetNumber = '888';
        $streetFull = "$streetName $streetNumber";
        $postNumber = '654321';

        $address->setStreetFull($streetFull);
        $address->setCompany($postNumber);

        $receiver = $helper->shippingAddressToReceiver($address);

        $this->assertSame($streetNumber, $receiver->getPostfiliale()->getPostfilialNumber());
        $this->assertSame($postNumber, $receiver->getPostfiliale()->getPostNumber());
        $this->assertSame($postCode, $receiver->getPostfiliale()->getZip());
        $this->assertSame($city, $receiver->getPostfiliale()->getCity());
        $this->assertSame($country, $receiver->getPostfiliale()->getCountryISOCode());
        $this->assertNull($receiver->getPackstation());
        $this->assertNull($receiver->getParcelShop());

    }
}
