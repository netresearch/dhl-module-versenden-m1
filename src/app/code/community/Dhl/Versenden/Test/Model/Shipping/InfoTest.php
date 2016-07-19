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
     */
    public function loadDhlVersendenInfo($jsonInfo)
    {
        $shippingInfo = new ShippingInfo();
        $shippingInfo->setJson($jsonInfo);

        $this->assertInstanceOf(
            ShippingInfo\ServiceSettings::class,
            $shippingInfo->serviceSettings
        );
        $this->assertInstanceOf(
            ShippingInfo\Receiver::class,
            $shippingInfo->shippingAddress
        );
    }

    /**
     * @test
     */
    public function shiftDhlVersendenInfo()
    {
        // prepare service settings
        $preferredLocation = 'Foo';

        $settingsObj = new stdClass();
        $settingsObj->preferredLocation = $preferredLocation;
        $serviceSettings = new ShippingInfo\ServiceSettings($settingsObj);

        $streetNumber      = '303';
        $packstationNumber = '404';
        $postfilialNumber  = '505';
        $parcelShopNumber  = '606';

        // prepare receiver
        $packstationObject = new stdClass();
        $packstationObject->packstationNumber = $packstationNumber;
        $postfilialObject = new stdClass();
        $postfilialObject->postfilialNumber = $postfilialNumber;
        $parcelShopObject = new stdClass();
        $parcelShopObject->parcelShopNumber = $parcelShopNumber;

        $receiverObj = new stdClass();
        $receiverObj->streetNumber = $streetNumber;
        $receiverObj->packstation  = $packstationObject;
        $receiverObj->postfiliale  = $postfilialObject;
        $receiverObj->parcelShop   = $parcelShopObject;
        $receiver = new ShippingInfo\Receiver($receiverObj);

        // create and serialize shipping info
        $shippingInfo = new ShippingInfo($receiver, $serviceSettings);
        $json = $shippingInfo->getJson();

        // create shipping info from serialized string
        $shippingInfo = new ShippingInfo();
        $shippingInfo->setJson($json);
        $this->assertEquals($preferredLocation, $shippingInfo->serviceSettings->preferredLocation);
        $this->assertEquals($streetNumber, $shippingInfo->shippingAddress->streetNumber);

        $this->assertEquals($packstationNumber, $shippingInfo->shippingAddress->packstation->packstationNumber);
        $this->assertEquals($postfilialNumber, $shippingInfo->shippingAddress->postfiliale->postfilialNumber);
        $this->assertEquals($parcelShopNumber, $shippingInfo->shippingAddress->parcelShop->parcelShopNumber);
    }
}
