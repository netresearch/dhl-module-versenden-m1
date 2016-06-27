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
use Dhl\Versenden\Config\Shipper\Contact as ShipperContact;
use Dhl\Versenden\Config\Shipper\BankData as ShipperBankData;
use Dhl\Versenden\Config\Shipper\Account as ShipperAccount;
use Dhl\Versenden\Config\Shipper\ReturnReceiver;
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
     * @loadFixture ShipperTest
     */
    public function getShipperAccount()
    {
        $config = new Dhl_Versenden_Model_Config();

        $testAccount = $config->getShipperAccount();
        $this->assertInstanceOf(ShipperAccount::class, $testAccount);
        $this->assertEquals('2222222222_01', $testAccount->user);
        $this->assertEquals('pass', $testAccount->signature);
        $this->assertEquals('2222222222', $testAccount->ekp);
        $this->assertTrue($testAccount->goGreen);
        $this->assertEquals('01', $testAccount->participation->dhlPaket);
        $this->assertEquals('01', $testAccount->participation->dhlReturnShipment);

        $prodAccount = $config->getShipperAccount('store_two');
        $this->assertInstanceOf(ShipperAccount::class, $prodAccount);
        $this->assertEquals('303', $prodAccount->user);
        $this->assertEquals('magento', $prodAccount->signature);
        $this->assertEquals('4711xx4711', $prodAccount->ekp);
        $this->assertFalse($prodAccount->goGreen);
        $this->assertEquals('98', $prodAccount->participation->dhlPaket);
        $this->assertEquals('99', $prodAccount->participation->dhlReturnShipment);
    }

    /**
     * @test
     * @loadFixture ShipperTest
     */
    public function getShipperBankData()
    {
        $config = new Dhl_Versenden_Model_Config();

        $defaultData = $config->getShipperBankData();
        $this->assertInstanceOf(ShipperBankData::class, $defaultData);
        $this->assertEquals("Foo Name", $defaultData->accountOwner);
        $this->assertEquals("Foo Bank", $defaultData->bankName);
        $this->assertEquals("DE999", $defaultData->iban);
        $this->assertEquals("XXXXX", $defaultData->bic);
        $this->assertEquals("Foo Note", $defaultData->note1);
        $this->assertEquals("Foo Note 2", $defaultData->note2);
        $this->assertEquals("Foo Ref", $defaultData->accountReference);

        $storeData = $config->getShipperBankData('store_two');
        $this->assertInstanceOf(ShipperBankData::class, $storeData);
        $this->assertEquals("Bar Name", $storeData->accountOwner);
        $this->assertEquals("Bar Bank", $storeData->bankName);
        $this->assertEquals("AT999", $storeData->iban);
        $this->assertEquals("YYYYY", $storeData->bic);
        $this->assertEquals("Bar Note", $storeData->note1);
        $this->assertEquals("Bar Note 2", $storeData->note2);
        $this->assertEquals("Bar Ref", $storeData->accountReference);
    }

    /**
     * @test
     * @loadFixture ShipperTest
     */
    public function getShipperContact()
    {
        $config = new Dhl_Versenden_Model_Config();

        $defaultContact = $config->getShipperContact();
        $this->assertInstanceOf(ShipperContact::class, $defaultContact);
        $this->assertEquals("Foo Name", $defaultContact->name1);
        $this->assertEquals("Foo Name 2", $defaultContact->name2);
        $this->assertEquals("Foo Name 3", $defaultContact->name3);
        $this->assertEquals("Foo Street", $defaultContact->streetName);
        $this->assertEquals("33", $defaultContact->streetNumber);
        $this->assertEquals("Floor 33", $defaultContact->addressAddition);
        $this->assertEquals("D0I", $defaultContact->dispatchingInformation);
        $this->assertEquals("A1111", $defaultContact->zip);
        $this->assertEquals("Foo City", $defaultContact->city);
        $this->assertEquals("Germany", $defaultContact->country);
        $this->assertEquals("DE", $defaultContact->countryISOCode);
        $this->assertEquals("1234", $defaultContact->phone);
        $this->assertEquals("a@foo", $defaultContact->email);
        $this->assertEquals("Default Contact", $defaultContact->contactPerson);

        $storeContact = $config->getShipperContact('store_two');
        $this->assertInstanceOf(ShipperContact::class, $storeContact);
        $this->assertEquals("Bar Name", $storeContact->name1);
        $this->assertEquals("Bar Name 2", $storeContact->name2);
        $this->assertEquals("Bar Name 3", $storeContact->name3);
        $this->assertEquals("Bar Street", $storeContact->streetName);
        $this->assertEquals("44b", $storeContact->streetNumber);
        $this->assertEquals("Floor 44", $storeContact->addressAddition);
        $this->assertEquals("D2I", $storeContact->dispatchingInformation);
        $this->assertEquals("B1111", $storeContact->zip);
        $this->assertEquals("Bar City", $storeContact->city);
        $this->assertEquals("Austria", $storeContact->country);
        $this->assertEquals("AT", $storeContact->countryISOCode);
        $this->assertEquals("9876", $storeContact->phone);
        $this->assertEquals("a@bar", $storeContact->email);
        $this->assertEquals("Store Contact", $storeContact->contactPerson);
    }

    /**
     * @test
     * @loadFixture ShipperTest
     */
    public function getReturnReceiver()
    {
        $config = new Dhl_Versenden_Model_Config();

        $shipperReceiver = $config->getReturnReceiver();
        $this->assertInstanceOf(ShipperContact::class, $shipperReceiver);
        $this->assertEquals("Foo Name", $shipperReceiver->name1);
        $this->assertEquals("Foo Name 2", $shipperReceiver->name2);
        $this->assertEquals("Foo Name 3", $shipperReceiver->name3);
        $this->assertEquals("Foo Street", $shipperReceiver->streetName);
        $this->assertEquals("33", $shipperReceiver->streetNumber);
        $this->assertEquals("Floor 33", $shipperReceiver->addressAddition);
        $this->assertEquals("D0I", $shipperReceiver->dispatchingInformation);
        $this->assertEquals("A1111", $shipperReceiver->zip);
        $this->assertEquals("Foo City", $shipperReceiver->city);
        $this->assertEquals("Germany", $shipperReceiver->country);
        $this->assertEquals("DE", $shipperReceiver->countryISOCode);
        $this->assertEquals("1234", $shipperReceiver->phone);
        $this->assertEquals("a@foo", $shipperReceiver->email);
        $this->assertEquals("Default Contact", $shipperReceiver->contactPerson);

        $storeReceiver = $config->getReturnReceiver('store_two');
        $this->assertInstanceOf(ShipperContact::class, $storeReceiver);
        $this->assertInstanceOf(ReturnReceiver::class, $storeReceiver);
        $this->assertEquals("Return Name", $storeReceiver->name1);
        $this->assertEquals("Return Name 2", $storeReceiver->name2);
        $this->assertEquals("Return Name 3", $storeReceiver->name3);
        $this->assertEquals("Return Street", $storeReceiver->streetName);
        $this->assertEquals("55r", $storeReceiver->streetNumber);
        $this->assertEquals("Floor 55", $storeReceiver->addressAddition);
        $this->assertEquals("DRI", $storeReceiver->dispatchingInformation);
        $this->assertEquals("R1111", $storeReceiver->zip);
        $this->assertEquals("Return City", $storeReceiver->city);
        $this->assertEquals("Switzerland", $storeReceiver->country);
        $this->assertEquals("CH", $storeReceiver->countryISOCode);
        $this->assertEquals("1010", $storeReceiver->phone);
        $this->assertEquals("a@ret", $storeReceiver->email);
        $this->assertEquals("Return Contact", $storeReceiver->contactPerson);
    }

    /**
     * @test
     * @loadFixture ShipperTest
     */
    public function getShipper()
    {
        $config = new Dhl_Versenden_Model_Config();

        $defaultShipper = $config->getShipper();
        $this->assertInstanceOf(Dhl\Versenden\Config\Shipper::class, $defaultShipper);
        $this->assertEquals('pass', $defaultShipper->account->signature);
        $this->assertEquals("DE999", $defaultShipper->bankData->iban);
        $this->assertEquals("Foo City", $defaultShipper->contact->city);
        $this->assertEquals("Foo City", $defaultShipper->returnReceiver->city);

        $storeShipper = $config->getShipper('store_two');
        $this->assertInstanceOf(Dhl\Versenden\Config\Shipper::class, $storeShipper);
        $this->assertEquals('magento', $storeShipper->account->signature);
        $this->assertEquals("AT999", $storeShipper->bankData->iban);
        $this->assertEquals("Bar City", $storeShipper->contact->city);
        $this->assertEquals("Return City", $storeShipper->returnReceiver->city);
    }
}
