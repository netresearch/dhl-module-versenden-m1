<?php

/**
 * See LICENSE.md for license details.
 */

use Dhl\Versenden\ParcelDe\Config\Data\Shipper;
use Dhl\Versenden\ParcelDe\Config\Data\Shipper\Account as ShipperAccount;
use Dhl\Versenden\ParcelDe\Config\Data\Shipper\BankData as ShipperBankData;
use Dhl\Versenden\ParcelDe\Config\Data\Shipper\Contact as ShipperContact;
use Dhl\Versenden\ParcelDe\Config\Data\Shipper\ReturnReceiver;

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
        static::assertInstanceOf(ShipperAccount::class, $testAccount);
        static::assertEquals('user-valid', $testAccount->getUser());
        static::assertEquals('SandboxPasswort2023!', $testAccount->getSignature());
        static::assertEquals('3333333333', $testAccount->getEkp());
        static::assertEquals('02', $testAccount->getParticipation('01'));
    }

    /**
     * @test
     * @loadFixture Model_ShipperConfigTest
     */
    public function getShipperBankData()
    {
        $config = new Dhl_Versenden_Model_Config_Shipper();

        $defaultData = $config->getBankData();
        static::assertInstanceOf(ShipperBankData::class, $defaultData);
        static::assertEquals('Foo Name', $defaultData->getAccountOwner());
        static::assertEquals('Foo Bank', $defaultData->getBankName());
        static::assertEquals('DE123', $defaultData->getIban());
        static::assertEquals('XXXXX', $defaultData->getBic());
        static::assertEquals('Foo Note', $defaultData->getNote1());
        static::assertEquals('Foo Note 2', $defaultData->getNote2());
        static::assertEquals('Foo Ref', $defaultData->getAccountReference());

        $storeData = $config->getBankData('store_two');
        static::assertInstanceOf(ShipperBankData::class, $storeData);
        static::assertEquals('Bar Name', $storeData->getAccountOwner());
        static::assertEquals('Bar Bank', $storeData->getBankName());
        static::assertEquals('DE987', $storeData->getIban());
        static::assertEquals('YYYYY', $storeData->getBic());
        static::assertEquals('Bar Note', $storeData->getNote1());
        static::assertEquals('Bar Note 2', $storeData->getNote2());
        static::assertEquals('Bar Ref', $storeData->getAccountReference());
    }

    /**
     * @test
     * @loadFixture Model_ShipperConfigTest
     */
    public function getShipperContact()
    {
        $config = new Dhl_Versenden_Model_Config_Shipper();

        $defaultContact = $config->getContact();
        static::assertInstanceOf(ShipperContact::class, $defaultContact);
        static::assertEquals('Foo Name', $defaultContact->getName1());
        static::assertEquals('Foo Name 2', $defaultContact->getName2());
        static::assertEquals('Foo Name 3', $defaultContact->getName3());
        static::assertEquals('Foo Street', $defaultContact->getStreetName());
        static::assertEquals('33', $defaultContact->getStreetNumber());
        static::assertEquals('Floor 33', $defaultContact->getAddressAddition());
        static::assertEquals('D0I', $defaultContact->getDispatchingInformation());
        static::assertEquals('A1111', $defaultContact->getZip());
        static::assertEquals('Foo City', $defaultContact->getCity());
        static::assertEquals('Germany', $defaultContact->getCountry());
        static::assertEquals('DE', $defaultContact->getCountryISOCode());
        static::assertEquals('1234', $defaultContact->getPhone());
        static::assertEquals('a@foo', $defaultContact->getEmail());
        static::assertEquals('Default Contact', $defaultContact->getContactPerson());

        $storeContact = $config->getContact('store_two');
        static::assertInstanceOf(ShipperContact::class, $storeContact);
        static::assertEquals('Bar Name', $storeContact->getName1());
        static::assertEquals('Bar Name 2', $storeContact->getName2());
        static::assertEquals('Bar Name 3', $storeContact->getName3());
        static::assertEquals('Bar Street', $storeContact->getStreetName());
        static::assertEquals('44b', $storeContact->getStreetNumber());
        static::assertEquals('Floor 44', $storeContact->getAddressAddition());
        static::assertEquals('D2I', $storeContact->getDispatchingInformation());
        static::assertEquals('B1111', $storeContact->getZip());
        static::assertEquals('Bar City', $storeContact->getCity());
        static::assertEquals('Germany', $storeContact->getCountry());
        static::assertEquals('DE', $storeContact->getCountryISOCode());
        static::assertEquals('9876', $storeContact->getPhone());
        static::assertEquals('a@bar', $storeContact->getEmail());
        static::assertEquals('Store Contact', $storeContact->getContactPerson());
    }

    /**
     * @test
     * @loadFixture Model_ShipperConfigTest
     */
    public function getReturnReceiver()
    {
        $config = new Dhl_Versenden_Model_Config_Shipper();

        $shipperReceiver = $config->getReturnReceiver();
        static::assertInstanceOf(ShipperContact::class, $shipperReceiver);
        static::assertEquals('Foo Name', $shipperReceiver->getName1());
        static::assertEquals('Foo Name 2', $shipperReceiver->getName2());
        static::assertEquals('Foo Name 3', $shipperReceiver->getName3());
        static::assertEquals('Foo Street', $shipperReceiver->getStreetName());
        static::assertEquals('33', $shipperReceiver->getStreetNumber());
        static::assertEquals('Floor 33', $shipperReceiver->getAddressAddition());
        static::assertEquals('D0I', $shipperReceiver->getDispatchingInformation());
        static::assertEquals('A1111', $shipperReceiver->getZip());
        static::assertEquals('Foo City', $shipperReceiver->getCity());
        static::assertEquals('Germany', $shipperReceiver->getCountry());
        static::assertEquals('DE', $shipperReceiver->getCountryISOCode());
        static::assertEquals('1234', $shipperReceiver->getPhone());
        static::assertEquals('a@foo', $shipperReceiver->getEmail());
        static::assertEquals('Default Contact', $shipperReceiver->getContactPerson());

        $storeReceiver = $config->getReturnReceiver('store_two');
        static::assertInstanceOf(ShipperContact::class, $storeReceiver);
        static::assertInstanceOf(ReturnReceiver::class, $storeReceiver);
        static::assertEquals('Return Name', $storeReceiver->getName1());
        static::assertEquals('Return Name 2', $storeReceiver->getName2());
        static::assertEquals('Return Name 3', $storeReceiver->getName3());
        static::assertEquals('Return Street', $storeReceiver->getStreetName());
        static::assertEquals('55r', $storeReceiver->getStreetNumber());
        static::assertEquals('Floor 55', $storeReceiver->getAddressAddition());
        static::assertEquals('DRI', $storeReceiver->getDispatchingInformation());
        static::assertEquals('R1111', $storeReceiver->getZip());
        static::assertEquals('Return City', $storeReceiver->getCity());
        static::assertEquals('Switzerland', $storeReceiver->getCountry());
        static::assertEquals('CH', $storeReceiver->getCountryISOCode());
        static::assertEquals('1010', $storeReceiver->getPhone());
        static::assertEquals('a@ret', $storeReceiver->getEmail());
        static::assertEquals('Return Contact', $storeReceiver->getContactPerson());
    }

    /**
     * @test
     * @loadFixture Model_ShipperConfigTest
     */
    public function getShipper()
    {
        $config = new Dhl_Versenden_Model_Config_Shipper();

        $defaultShipper = $config->getShipper();
        static::assertInstanceOf(Shipper::class, $defaultShipper);
        static::assertEquals('SandboxPasswort2023!', $defaultShipper->getAccount()->getSignature());
        static::assertEquals('DE123', $defaultShipper->getBankData()->getIban());
        static::assertEquals('Foo City', $defaultShipper->getContact()->getCity());
        static::assertEquals('Foo City', $defaultShipper->getReturnReceiver()->getCity());

        $storeShipper = $config->getShipper('store_two');
        static::assertInstanceOf(Shipper::class, $storeShipper);
        static::assertEquals('SandboxPasswort2023!', $storeShipper->getAccount()->getSignature());
        static::assertEquals('DE987', $storeShipper->getBankData()->getIban());
        static::assertEquals('Bar City', $storeShipper->getContact()->getCity());
        static::assertEquals('Return City', $storeShipper->getReturnReceiver()->getCity());
    }

    /**
     * @test
     * @loadFixture Model_ShipperConfigTest
     */
    public function getShipperCountry()
    {
        $config = new Dhl_Versenden_Model_Config_Shipper();

        static::assertEquals('DE', $config->getShipperCountry());
        static::assertEquals('DE', $config->getShipperCountry('store_two'));
    }
}
