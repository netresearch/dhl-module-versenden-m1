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
use \Dhl\Versenden\ShippingInfo;
/**
 * Dhl_Versenden_Test_Helper_DataTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Test_Helper_DataTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     */
    public function getModuleVersion()
    {
        $helper = Mage::helper('dhl_versenden/data');
        $this->assertRegExp('/\d\.\d{1,2}\.\d{1,2}/', $helper->getModuleVersion());
    }

    /**
     * @param string $street
     *
     * @test
     * @loadExpectation
     * @dataProvider dataProvider
     */
    public function splitStreet($street)
    {
        $helper   = Mage::helper('dhl_versenden/data');
        $split    = $helper->splitStreet($street);
        $expected = $this->expected('auto')->getData();

        $this->assertEquals($expected, $split);
    }

    /**
     * @test
     * @loadFixture getReceiver
     */
    public function getReceiverNoPostalFacility()
    {
        $address = Mage::getModel('sales/quote_address')->load(100);
        $helper = new Dhl_Versenden_Helper_Data();
        $receiver = $helper->getReceiver($address);
        $this->assertEquals('Charles-de-Gaulle-Straße', $receiver->streetName);
        $this->assertEquals('20', $receiver->streetNumber);
        $this->assertEmpty($receiver->packstation);
        $this->assertEmpty($receiver->postfiliale);
        $this->assertEmpty($receiver->parcelShop);
    }

    /**
     * @test
     * @loadFixture getReceiver
     */
    public function getReceiverWithPackstation()
    {
        $stationType = 'Packstation';
        $stationId   = '987';

        $street = "{$stationType} {$stationId}";
        $company = '1234567890';

        $address = Mage::getModel('sales/quote_address')->load(100);
        $address->setStreet($street);
        $address->setCompany($company);

        $helper = new Dhl_Versenden_Helper_Data();
        $receiver = $helper->getReceiver($address);

        $this->assertNotEmpty($receiver->packstation);
        $this->assertEquals($stationId, $receiver->packstation->packstationNumber);
        $this->assertEquals($company, $receiver->packstation->postNumber);

        $this->assertEmpty($receiver->postfiliale);
        $this->assertEmpty($receiver->parcelShop);
    }

    /**
     * @test
     * @loadFixture getReceiver
     */
    public function getReceiverWithPostfiliale()
    {
        $stationType = 'Postfiliale';
        $stationId   = '987';

        $street = "{$stationType} {$stationId}";
        $company = '1234567890';

        $address = Mage::getModel('sales/quote_address')->load(100);
        $address->setStreet($street);
        $address->setCompany($company);

        $helper = new Dhl_Versenden_Helper_Data();
        $receiver = $helper->getReceiver($address);

        $this->assertNotEmpty($receiver->postfiliale);
        $this->assertEquals($stationId, $receiver->postfiliale->postfilialNumber);
        $this->assertEquals($company, $receiver->postfiliale->postNumber);

        $this->assertEmpty($receiver->packstation);
        $this->assertEmpty($receiver->parcelShop);
    }

    /**
     * @test
     * @loadFixture getReceiver
     */
    public function getReceiverWithParcelShop()
    {
        // set "Postfiliale" temporarily to get through the observer method
        $stationType = 'Postfiliale';
        $stationId   = '987';

        $street = "{$stationType} {$stationId}";
        $company = '1234567890';

        $parcelShop = new ShippingInfo\ParcelShop();
        $parcelShop->parcelShopNumber = $stationId;
        $parcelShop->streetName = $stationType;
        $parcelShop->streetNumber = $stationId;

        $helperMock = $this->getHelperMock('dhl_versenden/data', array('preparePostalFacility'));
        $helperMock
            ->expects($this->once())
            ->method('preparePostalFacility')
            ->willReturn($parcelShop);
        $this->replaceByMock('helper', 'dhl_versenden/data', $helperMock);



        $address = Mage::getModel('sales/quote_address')->load(100);
        $address->setStreet($street);
        $address->setCompany($company);

        $helper = Mage::helper('dhl_versenden/data');
        $receiver = $helper->getReceiver($address);

        // The Dhl_Versenden module does not handle ParcelShops, use Dhl_LocationFinder
        $this->assertNotEmpty($receiver->parcelShop);
        $this->assertSame($stationId, $receiver->parcelShop->parcelShopNumber);
        $this->assertSame($stationType, $receiver->parcelShop->streetName);
        $this->assertSame($stationId, $receiver->parcelShop->streetNumber);
        $this->assertEmpty($receiver->packstation);
        $this->assertEmpty($receiver->postfiliale);
    }

    /**
     * @test
     */
    public function preparePostalFacility()
    {
        $address = Mage::getModel('sales/quote_address')->load(100);
        $streetName = 'Foo';
        $streetNumber = '909';

        $facility = new Varien_Object(array(
            'shop_type' => ShippingInfo\PostalFacility::TYPE_PAKETSHOP,
            'shop_number' => '808',
        ));

        $receiver = new ShippingInfo\Receiver();
        $receiver->zip = $address->getPostcode();
        $receiver->city = $address->getCity();
        $receiver->country = $address->getCountry();
        $receiver->countryISOCode = $address->getCountryId();
        $receiver->state = $address->getRegion();
        $receiver->streetName = $streetName;
        $receiver->streetNumber = $streetNumber;

        $helper = new Dhl_Versenden_Helper_Data();
        /** @var ShippingInfo\ParcelShop $station */
        $station = $helper->preparePostalFacility($facility, $receiver);
        $this->assertInstanceOf(ShippingInfo\ParcelShop::class, $station);
        $this->assertEquals($streetName, $station->streetName);
        $this->assertEquals($streetNumber, $station->streetNumber);
    }

    /**
     * @test
     * @loadFixture getReceiver
     */
    public function preparePostBox()
    {
        $address = Mage::getModel('sales/quote_address')->load(100);
        $streetName = 'Foo';
        $streetNumber = '909';

        $facility = new Varien_Object(array(
            'shop_type' => 'PostBox', // unrecognized station type
            'shop_number' => '808',
        ));

        $receiver = new ShippingInfo\Receiver();
        $receiver->zip = $address->getPostcode();
        $receiver->city = $address->getCity();
        $receiver->country = $address->getCountry();
        $receiver->countryISOCode = $address->getCountryId();
        $receiver->state = $address->getRegion();
        $receiver->streetName = $streetName;
        $receiver->streetNumber = $streetNumber;

        $helper = new Dhl_Versenden_Helper_Data();
        /** @var ShippingInfo\ParcelShop $station */
        $station = $helper->preparePostalFacility($facility, $receiver);
        $this->assertNull($station);
    }
}
