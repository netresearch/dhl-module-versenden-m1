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
        $stdObject = json_decode($jsonInfo);
        $shippingInfo = \Dhl\Versenden\Webservice\RequestData\ObjectMapper::getShippingInfo($stdObject);

        $this->assertInstanceOf(
            ServiceSelection::class,
            $shippingInfo->getServiceSelection()
        );
        $this->assertInstanceOf(
            Receiver::class,
            $shippingInfo->getReceiver()
        );
    }

    /**
     * @test
     */
    public function shiftDhlVersendenInfo()
    {
        // prepare service settings
        $preferredLocation = 'Foo';

        $serviceSelection = ServiceSelection::fromProperties(
            false,
            false,
            $preferredLocation,
            false,
            false,
            false,
            false,
            false,
            false
        );


        $streetName        = 'Street';
        $streetNumber      = '303';


        $packstationNumber = '404';
        $postfilialNumber  = '505';
        $parcelShopNumber  = '606';
        $postNumber        = '707';

        $receiver = new Receiver(
            'Foo Name',
            '',
            '',
            $streetName,
            $streetNumber,
            '',
            '',
            'Foo Zip',
            'Foo City',
            '',
            'DE',
            '',
            '',
            '',
            '',
            new Receiver\Packstation('', '', $packstationNumber, $postNumber),
            new Receiver\Postfiliale('', '', $postfilialNumber, $postNumber),
            new Receiver\ParcelShop('', '', $parcelShopNumber, $streetName, $streetNumber)
        );

        // create and serialize shipping info
        $shippingInfo = new ShippingInfo($receiver, $serviceSelection);
        $json = json_encode($shippingInfo, JSON_FORCE_OBJECT);


        // create shipping info from serialized string
        $stdObject = json_decode($json);
        $shippingInfo = \Dhl\Versenden\Webservice\RequestData\ObjectMapper::getShippingInfo($stdObject);

        $this->assertEquals($preferredLocation, $shippingInfo->getServiceSelection()->getPreferredLocation());

        $this->assertEquals($streetNumber, $shippingInfo->getReceiver()->getStreetNumber());
        $this->assertEquals($streetName, $shippingInfo->getReceiver()->getStreetName());
        $this->assertEquals($streetName, $shippingInfo->getReceiver()->getParcelShop()->getStreetName());

        $this->assertEquals($packstationNumber, $shippingInfo->getReceiver()->getPackstation()->getPackstationNumber());
        $this->assertEquals($postfilialNumber, $shippingInfo->getReceiver()->getPostfiliale()->getPostfilialNumber());
        $this->assertEquals($parcelShopNumber, $shippingInfo->getReceiver()->getParcelShop()->getParcelShopNumber());
        $this->assertEquals($postNumber, $shippingInfo->getReceiver()->getPackstation()->getPostNumber());
        $this->assertEquals($postNumber, $shippingInfo->getReceiver()->getPostfiliale()->getPostNumber());
    }
}
