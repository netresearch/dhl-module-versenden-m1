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
use \Dhl\Versenden\Webservice\RequestData\ShippingInfo;
use \Dhl\Versenden\Webservice\RequestData\ShipmentOrder\Receiver;
use \Dhl\Versenden\Webservice\RequestData\ShipmentOrder\ServiceSelection;
/**
 * Dhl_Versenden_Test_Model_Shipping_InfoTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Test_Model_Shipping_InfoTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     * @dataProvider dataProvider
     *
     * @param string $jsonInfo
     */
    public function loadDhlVersendenInfo($jsonInfo)
    {
        $shippingInfo = \Dhl\Versenden\Info\Serializer::unserialize($jsonInfo);
        $this->assertInstanceOf(
            \Dhl\Versenden\Info::class,
            $shippingInfo
        );
        $this->assertInstanceOf(
            \Dhl\Versenden\Info\Services::class,
            $shippingInfo->getServices()
        );
        $this->assertInstanceOf(
            \Dhl\Versenden\Info\Receiver::class,
            $shippingInfo->getReceiver()
        );
    }

    /**
     * Assert that after unserialization all information is still the same as
     * before serialization.
     *
     * @test
     */
    public function shiftDhlVersendenInfo()
    {
        // prepare service settings
        $preferredLocation = 'Foo';

        $streetName        = 'Street';
        $streetNumber      = '303';
        $countryCode       = 'DE';

        $packstationNumber = '404';
        $postfilialNumber  = '505';
        $parcelShopNumber  = '606';
        $postNumber        = '707';


        // create and serialize shipping info
        $shippingInfo = new \Dhl\Versenden\Info();
        $shippingInfo->getServices()->preferredLocation = $preferredLocation;

        $shippingInfo->getReceiver()->streetName = $streetName;
        $shippingInfo->getReceiver()->streetNumber = $streetNumber;
        $shippingInfo->getReceiver()->countryISOCode = $countryCode;

        $shippingInfo->getReceiver()->getPackstation()->packstationNumber = $packstationNumber;
        $shippingInfo->getReceiver()->getPackstation()->postNumber = $postNumber;
        $shippingInfo->getReceiver()->getPackstation()->countryISOCode = $countryCode;

        $shippingInfo->getReceiver()->getPostfiliale()->postfilialNumber = $postfilialNumber;
        $shippingInfo->getReceiver()->getPostfiliale()->postNumber = $postNumber;
        $shippingInfo->getReceiver()->getPostfiliale()->countryISOCode = $countryCode;

        $shippingInfo->getReceiver()->getParcelShop()->parcelShopNumber = $parcelShopNumber;
        $shippingInfo->getReceiver()->getParcelShop()->streetName = $streetName;
        $shippingInfo->getReceiver()->getParcelShop()->streetNumber = $streetNumber;
        $shippingInfo->getReceiver()->getParcelShop()->countryISOCode = $countryCode;

        $json = \Dhl\Versenden\Info\Serializer::serialize($shippingInfo);


        // create shipping info from serialized string
        $unserialized = \Dhl\Versenden\Info\Serializer::unserialize($json);

        $this->assertEquals($preferredLocation, $unserialized->getServices()->preferredLocation);
        $this->assertNull($unserialized->getServices()->preferredNeighbour);

        $this->assertEquals($streetNumber, $unserialized->getReceiver()->streetNumber);
        $this->assertEquals($streetName, $unserialized->getReceiver()->streetName);
        $this->assertEquals($countryCode, $unserialized->getReceiver()->countryISOCode);

        $this->assertEquals($packstationNumber, $unserialized->getReceiver()->getPackstation()->packstationNumber);
        $this->assertEquals($postNumber, $unserialized->getReceiver()->getPackstation()->postNumber);
        $this->assertEquals($countryCode, $unserialized->getReceiver()->getPackstation()->countryISOCode);

        $this->assertEquals($postfilialNumber, $unserialized->getReceiver()->getPostfiliale()->postfilialNumber);
        $this->assertEquals($postNumber, $unserialized->getReceiver()->getPostfiliale()->postNumber);
        $this->assertEquals($countryCode, $unserialized->getReceiver()->getPostfiliale()->countryISOCode);

        $this->assertEquals($parcelShopNumber, $unserialized->getReceiver()->getParcelShop()->parcelShopNumber);
        $this->assertEquals($streetName, $unserialized->getReceiver()->getParcelShop()->streetName);
        $this->assertEquals($streetNumber, $unserialized->getReceiver()->getParcelShop()->streetNumber);
        $this->assertEquals($countryCode, $unserialized->getReceiver()->getParcelShop()->countryISOCode);
    }
}
