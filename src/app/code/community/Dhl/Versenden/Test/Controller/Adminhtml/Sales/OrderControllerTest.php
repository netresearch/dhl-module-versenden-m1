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
 * Dhl_Versenden_Test_Controller_Adminhtml_Sales_OrderControllerTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Test_Controller_Adminhtml_Sales_OrderControllerTest
    extends Dhl_Versenden_Test_Case_AdminController
{
    /**
     * @test
     * @loadFixture Controller_ConfigTest
     * @loadFixture Controller_EditAddressTest
     */
    public function addressSaveActionInvalidAddress()
    {
        $this->getRequest()->setPost(array());
        $this->dispatch('adminhtml/sales_order/addressSave', array('address_id' => 99));
        $this->assertRedirectTo('adminhtml/sales_order/index');
    }

    /**
     * @test
     * @loadFixture Controller_ConfigTest
     * @loadFixture Controller_EditAddressTest
     */
    public function addressSaveActionError()
    {
        $telephone = '1234567890';

        $message = 'Foo.';
        $addressMock = $this->getModelMock('sales/order_address', array('save'));
        $addressMock
            ->expects($this->once())
            ->method('save')
            ->willThrowException(new Mage_Core_Exception($message));
        $this->replaceByMock('model', 'sales/order_address', $addressMock);

        $sessionMock = $this->getModelMock('adminhtml/session', array('addError'));
        $sessionMock
            ->expects($this->once())
            ->method('addError')
            ->with($message);
        $this->replaceByMock('singleton', 'adminhtml/session', $sessionMock);

        $postData = array(
            'telephone' => $telephone,
        );
        $this->getRequest()->setPost($postData);
        $this->dispatch('adminhtml/sales_order/addressSave', array('address_id' => 100));
        $this->assertRedirectTo('adminhtml/sales_order/address', array('address_id' => 100));
    }

    /**
     * @test
     * @loadFixture Controller_ConfigTest
     * @loadFixture Controller_EditAddressTest
     */
    public function addressSaveActionException()
    {
        $telephone = '1234567890';

        $exception = new Exception('Foo.');
        $addressMock = $this->getModelMock('sales/order_address', array('save'));
        $addressMock
            ->expects($this->once())
            ->method('save')
            ->willThrowException($exception);
        $this->replaceByMock('model', 'sales/order_address', $addressMock);

        $sessionMock = $this->getModelMock('adminhtml/session', array('addException'));
        $sessionMock
            ->expects($this->once())
            ->method('addException')
            ->with($exception, $this->stringContains('The address has not been changed.'));
        $this->replaceByMock('singleton', 'adminhtml/session', $sessionMock);

        $postData = array(
            'telephone' => $telephone,
        );
        $this->getRequest()->setPost($postData);
        $this->dispatch('adminhtml/sales_order/addressSave', array('address_id' => 100));
        $this->assertRedirectTo('adminhtml/sales_order/address', array('address_id' => 100));
    }

    /**
     * @test
     * @loadFixture Controller_ConfigTest
     * @loadFixture Controller_EditAddressTest
     */
    public function addressSaveActionDefault()
    {
        $telephone = '1234567890';

        $sessionMock = $this->getModelMock('adminhtml/session', array('addSuccess'));
        $sessionMock
            ->expects($this->once())
            ->method('addSuccess')
            ->with($this->stringContains('The order address has been updated.'));
        $this->replaceByMock('singleton', 'adminhtml/session', $sessionMock);

        $postData = array(
            'telephone' => $telephone,
        );
        $this->getRequest()->setPost($postData);
        $this->dispatch('adminhtml/sales_order/addressSave', array('address_id' => 170));
        $this->assertRedirectTo('adminhtml/sales_order/view', array('order_id' => 17));

        $address = Mage::getModel('sales/order_address')->load(170);
        $this->assertEmpty($address->getData('dhl_versenden_info'));
    }

    /**
     * @test
     * @loadFixture Controller_ConfigTest
     * @loadFixture Controller_EditAddressTest
     */
    public function addressSaveActionOk()
    {
        $streetName = 'Charles-de-Gaulle-Str.';
        $streetNumber = '77';
        $telephone = '1234567890';
        $facilityNumber = '123';

        $sessionMock = $this->getModelMock('adminhtml/session', array('addSuccess'));
        $sessionMock
            ->expects($this->once())
            ->method('addSuccess')
            ->with($this->stringContains('The order address has been updated.'));
        $this->replaceByMock('singleton', 'adminhtml/session', $sessionMock);

        $postData = array(
            'telephone' => $telephone,
            'versenden_info' => array(
                'street_name' => $streetName,
                'street_number' => $streetNumber,
                'packstation' => array(
                    'packstation_number' => $facilityNumber,
                ),
                'postfiliale' => array(
                    'postfilial_number' => $facilityNumber,
                ),
                'parcelshop' => array(
                    'parcelshop_number' => $facilityNumber,
                ),
            ),
        );
        $this->getRequest()->setPost($postData);
        $this->dispatch('adminhtml/sales_order/addressSave', array('address_id' => 100));
        $this->assertRedirectTo('adminhtml/sales_order/view', array('order_id' => 10));
        $this->assertEventDispatched('dhl_versenden_announce_postal_facility');

        $address = Mage::getModel('sales/order_address')->load(100);
        /** @var \Dhl\Versenden\Info $versendenInfo */
        $versendenInfo = $address->getData('dhl_versenden_info');
        $this->assertInstanceOf(\Dhl\Versenden\Info::class, $versendenInfo);
        $this->assertEquals($streetName, $versendenInfo->getReceiver()->streetName);
        $this->assertEquals($streetNumber, $versendenInfo->getReceiver()->streetNumber);
        $this->assertEmpty($versendenInfo->getReceiver()->packstation->packstationNumber);
        $this->assertEmpty($versendenInfo->getReceiver()->postfiliale->postfilialNumber);
        $this->assertEmpty($versendenInfo->getReceiver()->parcelShop->parcelShopNumber);
    }

    /**
     * @test
     * @loadFixture Controller_ConfigTest
     * @loadFixture Controller_EditAddressTest
     */
    public function addressSavePackstationOk()
    {
        $streetName = 'Charles-de-Gaulle-Str.';
        $streetNumber = '77';
        $telephone = '1234567890';
        $packStationNumber = '123';
        $postNumber = '12345678';

        $sessionMock = $this->getModelMock('adminhtml/session', array('addSuccess'));
        $sessionMock
            ->expects($this->once())
            ->method('addSuccess')
            ->with($this->stringContains('The order address has been updated.'));
        $this->replaceByMock('singleton', 'adminhtml/session', $sessionMock);

        $postData = array(
            'telephone' => $telephone,
            'versenden_info' => array(
                'street_name' => $streetName,
                'street_number' => $streetNumber,
                'packstation' => array(
                    'packstation_number' => $packStationNumber,
                    'post_number' => $postNumber,
                ),
            ),
        );
        $this->getRequest()->setPost($postData);
        $this->dispatch('adminhtml/sales_order/addressSave', array('address_id' => 100));
        $this->assertRedirectTo('adminhtml/sales_order/view', array('order_id' => 10));
        $this->assertEventDispatched('dhl_versenden_announce_postal_facility');

        $address = Mage::getModel('sales/order_address')->load(100);
        /** @var \Dhl\Versenden\Info $versendenInfo */
        $versendenInfo = $address->getData('dhl_versenden_info');
        $this->assertInstanceOf(\Dhl\Versenden\Info::class, $versendenInfo);
        $this->assertEquals($streetName, $versendenInfo->getReceiver()->streetName);
        $this->assertEquals($streetNumber, $versendenInfo->getReceiver()->streetNumber);
        $this->assertEquals($packStationNumber, $versendenInfo->getReceiver()->packstation->packstationNumber);
        $this->assertEquals($postNumber, $versendenInfo->getReceiver()->packstation->postNumber);
        $this->assertEmpty($versendenInfo->getReceiver()->postfiliale->postfilialNumber);
        $this->assertEmpty($versendenInfo->getReceiver()->parcelShop->parcelShopNumber);
    }

    /**
     * @test
     * @loadFixture Controller_ConfigTest
     * @loadFixture Controller_EditAddressTest
     */
    public function addressSavePostfilialeOk()
    {
        $streetName = 'Charles-de-Gaulle-Str.';
        $streetNumber = '77';
        $telephone = '1234567890';
        $postfilialNumber = '123';
        $postNumber = '12345678';

        $sessionMock = $this->getModelMock('adminhtml/session', array('addSuccess'));
        $sessionMock
            ->expects($this->once())
            ->method('addSuccess')
            ->with($this->stringContains('The order address has been updated.'));
        $this->replaceByMock('singleton', 'adminhtml/session', $sessionMock);

        $postData = array(
            'telephone' => $telephone,
            'versenden_info' => array(
                'street_name' => $streetName,
                'street_number' => $streetNumber,
                'postfiliale' => array(
                    'postfilial_number' => $postfilialNumber,
                    'post_number' => $postNumber,
                ),
            ),
        );
        $this->getRequest()->setPost($postData);
        $this->dispatch('adminhtml/sales_order/addressSave', array('address_id' => 100));
        $this->assertRedirectTo('adminhtml/sales_order/view', array('order_id' => 10));
        $this->assertEventDispatched('dhl_versenden_announce_postal_facility');

        $address = Mage::getModel('sales/order_address')->load(100);
        /** @var \Dhl\Versenden\Info $versendenInfo */
        $versendenInfo = $address->getData('dhl_versenden_info');
        $this->assertInstanceOf(\Dhl\Versenden\Info::class, $versendenInfo);
        $this->assertEquals($streetName, $versendenInfo->getReceiver()->streetName);
        $this->assertEquals($streetNumber, $versendenInfo->getReceiver()->streetNumber);
        $this->assertEmpty($versendenInfo->getReceiver()->packstation->packstationNumber);
        $this->assertEquals($postfilialNumber, $versendenInfo->getReceiver()->postfiliale->postfilialNumber);
        $this->assertEquals($postNumber, $versendenInfo->getReceiver()->postfiliale->postNumber);
        $this->assertEmpty($versendenInfo->getReceiver()->parcelShop->parcelShopNumber);
    }

    /**
     * @test
     * @loadFixture Controller_ConfigTest
     * @loadFixture Controller_EditAddressTest
     */
    public function addressSaveParcelShopOk()
    {
        $streetName = 'Charles-de-Gaulle-Str.';
        $streetNumber = '77';
        $telephone = '1234567890';
        $parcelShopNumber = '123';

        $sessionMock = $this->getModelMock('adminhtml/session', array('addSuccess'));
        $sessionMock
            ->expects($this->once())
            ->method('addSuccess')
            ->with($this->stringContains('The order address has been updated.'));
        $this->replaceByMock('singleton', 'adminhtml/session', $sessionMock);

        $postData = array(
            'telephone' => $telephone,
            'versenden_info' => array(
                'street_name' => $streetName,
                'street_number' => $streetNumber,
                'parcelshop' => array(
                    'parcelshop_number' => $parcelShopNumber,
                    'street_name' => $streetName,
                    'street_number' => $streetNumber,
                ),
            ),
        );
        $this->getRequest()->setPost($postData);
        $this->dispatch('adminhtml/sales_order/addressSave', array('address_id' => 100));
        $this->assertRedirectTo('adminhtml/sales_order/view', array('order_id' => 10));
        $this->assertEventDispatched('dhl_versenden_announce_postal_facility');

        $address = Mage::getModel('sales/order_address')->load(100);
        /** @var \Dhl\Versenden\Info $versendenInfo */
        $versendenInfo = $address->getData('dhl_versenden_info');
        $this->assertInstanceOf(\Dhl\Versenden\Info::class, $versendenInfo);
        $this->assertEquals($streetName, $versendenInfo->getReceiver()->streetName);
        $this->assertEquals($streetNumber, $versendenInfo->getReceiver()->streetNumber);
        $this->assertEmpty($versendenInfo->getReceiver()->packstation->packstationNumber);
        $this->assertEmpty($versendenInfo->getReceiver()->postfiliale->postfilialNumber);
        $this->assertEquals($parcelShopNumber, $versendenInfo->getReceiver()->parcelShop->parcelShopNumber);
        $this->assertEquals($streetName, $versendenInfo->getReceiver()->parcelShop->streetName);
        $this->assertEquals($streetNumber, $versendenInfo->getReceiver()->parcelShop->streetNumber);
    }
}
