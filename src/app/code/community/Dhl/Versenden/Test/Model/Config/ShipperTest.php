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
use \Netresearch\Dhl\Versenden\Webservice\RequestData\ShipmentOrder\Shipper;
use \Netresearch\Dhl\Versenden\Webservice\RequestData\ShipmentOrder\Shipper\Account as ShipperAccount;
use \Netresearch\Dhl\Versenden\Webservice\RequestData\ShipmentOrder\Shipper\BankData as ShipperBankData;
use \Netresearch\Dhl\Versenden\Webservice\RequestData\ShipmentOrder\Shipper\Contact as ShipperContact;
use \Netresearch\Dhl\Versenden\Webservice\RequestData\ShipmentOrder\Shipper\ReturnReceiver;
/**
 * Dhl_Versenden_Test_Model_Config_ShipperTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Test_Model_Config_ShipperTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     * @loadFixture Model_ShipperConfigTest
     */
    public function getShipperAccount()
    {
        $config = new Dhl_Versenden_Model_Config_Shipper();

        $testAccount = $config->getAccountSettings();
        $this->assertInstanceOf(ShipperAccount::class, $testAccount);
        $this->assertEquals('2222222222_01', $testAccount->getUser());
        $this->assertEquals('pass', $testAccount->getSignature());
        $this->assertEquals('2222222222', $testAccount->getEkp());
        $this->assertEquals('04', $testAccount->getParticipation('01'));
    }

    /**
     * @test
     * @loadFixture Model_ShipperConfigTest
     */
    public function getShipperBankData()
    {
        $config = new Dhl_Versenden_Model_Config_Shipper();

        $defaultData = $config->getBankData();
        $this->assertInstanceOf(ShipperBankData::class, $defaultData);
        $this->assertEquals("Foo Name", $defaultData->getAccountOwner());
        $this->assertEquals("Foo Bank", $defaultData->getBankName());
        $this->assertEquals("DE999", $defaultData->getIban());
        $this->assertEquals("XXXXX", $defaultData->getBic());
        $this->assertEquals("Foo Note", $defaultData->getNote1());
        $this->assertEquals("Foo Note 2", $defaultData->getNote2());
        $this->assertEquals("Foo Ref", $defaultData->getAccountReference());

        $storeData = $config->getBankData('store_two');
        $this->assertInstanceOf(ShipperBankData::class, $storeData);
        $this->assertEquals("Bar Name", $storeData->getAccountOwner());
        $this->assertEquals("Bar Bank", $storeData->getBankName());
        $this->assertEquals("AT999", $storeData->getIban());
        $this->assertEquals("YYYYY", $storeData->getBic());
        $this->assertEquals("Bar Note", $storeData->getNote1());
        $this->assertEquals("Bar Note 2", $storeData->getNote2());
        $this->assertEquals("Bar Ref", $storeData->getAccountReference());
    }

    /**
     * @test
     * @loadFixture Model_ShipperConfigTest
     */
    public function getShipperContact()
    {
        $config = new Dhl_Versenden_Model_Config_Shipper();

        $defaultContact = $config->getContact();
        $this->assertInstanceOf(ShipperContact::class, $defaultContact);
        $this->assertEquals("Foo Name", $defaultContact->getName1());
        $this->assertEquals("Foo Name 2", $defaultContact->getName2());
        $this->assertEquals("Foo Name 3", $defaultContact->getName3());
        $this->assertEquals("Foo Street", $defaultContact->getStreetName());
        $this->assertEquals("33", $defaultContact->getStreetNumber());
        $this->assertEquals("Floor 33", $defaultContact->getAddressAddition());
        $this->assertEquals("D0I", $defaultContact->getDispatchingInformation());
        $this->assertEquals("A1111", $defaultContact->getZip());
        $this->assertEquals("Foo City", $defaultContact->getCity());
        $this->assertEquals("Germany", $defaultContact->getCountry());
        $this->assertEquals("DE", $defaultContact->getCountryISOCode());
        $this->assertEquals("1234", $defaultContact->getPhone());
        $this->assertEquals("a@foo", $defaultContact->getEmail());
        $this->assertEquals("Default Contact", $defaultContact->getContactPerson());

        $storeContact = $config->getContact('store_two');
        $this->assertInstanceOf(ShipperContact::class, $storeContact);
        $this->assertEquals("Bar Name", $storeContact->getName1());
        $this->assertEquals("Bar Name 2", $storeContact->getName2());
        $this->assertEquals("Bar Name 3", $storeContact->getName3());
        $this->assertEquals("Bar Street", $storeContact->getStreetName());
        $this->assertEquals("44b", $storeContact->getStreetNumber());
        $this->assertEquals("Floor 44", $storeContact->getAddressAddition());
        $this->assertEquals("D2I", $storeContact->getDispatchingInformation());
        $this->assertEquals("B1111", $storeContact->getZip());
        $this->assertEquals("Bar City", $storeContact->getCity());
        $this->assertEquals("Austria", $storeContact->getCountry());
        $this->assertEquals("AT", $storeContact->getCountryISOCode());
        $this->assertEquals("9876", $storeContact->getPhone());
        $this->assertEquals("a@bar", $storeContact->getEmail());
        $this->assertEquals("Store Contact", $storeContact->getContactPerson());
    }

    /**
     * @test
     * @loadFixture Model_ShipperConfigTest
     */
    public function getReturnReceiver()
    {
        $config = new Dhl_Versenden_Model_Config_Shipper();

        $shipperReceiver = $config->getReturnReceiver();
        $this->assertInstanceOf(ShipperContact::class, $shipperReceiver);
        $this->assertEquals("Foo Name", $shipperReceiver->getName1());
        $this->assertEquals("Foo Name 2", $shipperReceiver->getName2());
        $this->assertEquals("Foo Name 3", $shipperReceiver->getName3());
        $this->assertEquals("Foo Street", $shipperReceiver->getStreetName());
        $this->assertEquals("33", $shipperReceiver->getStreetNumber());
        $this->assertEquals("Floor 33", $shipperReceiver->getAddressAddition());
        $this->assertEquals("D0I", $shipperReceiver->getDispatchingInformation());
        $this->assertEquals("A1111", $shipperReceiver->getZip());
        $this->assertEquals("Foo City", $shipperReceiver->getCity());
        $this->assertEquals("Germany", $shipperReceiver->getCountry());
        $this->assertEquals("DE", $shipperReceiver->getCountryISOCode());
        $this->assertEquals("1234", $shipperReceiver->getPhone());
        $this->assertEquals("a@foo", $shipperReceiver->getEmail());
        $this->assertEquals("Default Contact", $shipperReceiver->getContactPerson());

        $storeReceiver = $config->getReturnReceiver('store_two');
        $this->assertInstanceOf(ShipperContact::class, $storeReceiver);
        $this->assertInstanceOf(ReturnReceiver::class, $storeReceiver);
        $this->assertEquals("Return Name", $storeReceiver->getName1());
        $this->assertEquals("Return Name 2", $storeReceiver->getName2());
        $this->assertEquals("Return Name 3", $storeReceiver->getName3());
        $this->assertEquals("Return Street", $storeReceiver->getStreetName());
        $this->assertEquals("55r", $storeReceiver->getStreetNumber());
        $this->assertEquals("Floor 55", $storeReceiver->getAddressAddition());
        $this->assertEquals("DRI", $storeReceiver->getDispatchingInformation());
        $this->assertEquals("R1111", $storeReceiver->getZip());
        $this->assertEquals("Return City", $storeReceiver->getCity());
        $this->assertEquals("Switzerland", $storeReceiver->getCountry());
        $this->assertEquals("CH", $storeReceiver->getCountryISOCode());
        $this->assertEquals("1010", $storeReceiver->getPhone());
        $this->assertEquals("a@ret", $storeReceiver->getEmail());
        $this->assertEquals("Return Contact", $storeReceiver->getContactPerson());
    }

    /**
     * @test
     * @loadFixture Model_ShipperConfigTest
     */
    public function getShipper()
    {
        $config = new Dhl_Versenden_Model_Config_Shipper();

        $defaultShipper = $config->getShipper();
        $this->assertInstanceOf(Shipper::class, $defaultShipper);
        $this->assertEquals('pass', $defaultShipper->getAccount()->getSignature());
        $this->assertEquals("DE999", $defaultShipper->getBankData()->getIban());
        $this->assertEquals("Foo City", $defaultShipper->getContact()->getCity());
        $this->assertEquals("Foo City", $defaultShipper->getReturnReceiver()->getCity());

        $storeShipper = $config->getShipper('store_two');
        $this->assertInstanceOf(Shipper::class, $storeShipper);
        $this->assertEquals('pass', $storeShipper->getAccount()->getSignature());
        $this->assertEquals("AT999", $storeShipper->getBankData()->getIban());
        $this->assertEquals("Bar City", $storeShipper->getContact()->getCity());
        $this->assertEquals("Return City", $storeShipper->getReturnReceiver()->getCity());
    }

    /**
     * @test
     * @loadFixture Model_ShipperConfigTest
     */
    public function getShipperCountry()
    {
        $config = new Dhl_Versenden_Model_Config_Shipper();

        $this->assertEquals('DE', $config->getShipperCountry());
        $this->assertEquals('AT', $config->getShipperCountry('store_two'));
    }
}
