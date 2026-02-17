<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Test_Controller_Adminhtml_Sales_OrderControllerTest extends Dhl_Versenden_Test_Case_AdminController
{
    /**
     * @test
     * @loadFixture Controller_ConfigTest
     * @loadFixture Controller_EditAddressTest
     */
    public function addressSaveActionInvalidAddress()
    {
        $this->getRequest()->setPost([]);
        $this->dispatch('adminhtml/sales_order/addressSave', ['address_id' => 99]);
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
        $addressMock = $this->getModelMock('sales/order_address', ['save']);
        $addressMock
            ->expects(static::once())
            ->method('save')
            ->willThrowException(new Mage_Core_Exception($message));
        $this->replaceByMock('model', 'sales/order_address', $addressMock);

        $sessionMock = $this->getModelMock('adminhtml/session', ['addError']);
        $sessionMock
            ->expects(static::once())
            ->method('addError')
            ->with($message);
        $this->replaceByMock('singleton', 'adminhtml/session', $sessionMock);

        $postData = [
            'telephone' => $telephone,
        ];
        $this->getRequest()->setPost($postData);
        $this->dispatch('adminhtml/sales_order/addressSave', ['address_id' => 100]);
        $this->assertRedirectTo('adminhtml/sales_order/address', ['address_id' => 100]);
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
        $addressMock = $this->getModelMock('sales/order_address', ['save']);
        $addressMock
            ->expects(static::once())
            ->method('save')
            ->willThrowException($exception);
        $this->replaceByMock('model', 'sales/order_address', $addressMock);

        $sessionMock = $this->getModelMock('adminhtml/session', ['addException']);
        $sessionMock
            ->expects(static::once())
            ->method('addException')
            ->with($exception, static::stringContains('The address has not been changed.'));
        $this->replaceByMock('singleton', 'adminhtml/session', $sessionMock);

        $postData = [
            'telephone' => $telephone,
        ];
        $this->getRequest()->setPost($postData);
        $this->dispatch('adminhtml/sales_order/addressSave', ['address_id' => 100]);
        $this->assertRedirectTo('adminhtml/sales_order/address', ['address_id' => 100]);
    }

    /**
     * @test
     * @loadFixture Controller_ConfigTest
     * @loadFixture Controller_EditAddressTest
     */
    public function addressSaveActionDefault()
    {
        $telephone = '1234567890';

        $sessionMock = $this->getModelMock('adminhtml/session', ['addSuccess']);
        $sessionMock
            ->expects(static::once())
            ->method('addSuccess')
            ->with(static::stringContains('The order address has been updated.'));
        $this->replaceByMock('singleton', 'adminhtml/session', $sessionMock);

        $postData = [
            'telephone' => $telephone,
        ];
        $this->getRequest()->setPost($postData);
        $this->dispatch('adminhtml/sales_order/addressSave', ['address_id' => 170]);
        $this->assertRedirectTo('adminhtml/sales_order/view', ['order_id' => 17]);

        $address = Mage::getModel('sales/order_address')->load(170);
        static::assertEmpty($address->getData('dhl_versenden_info'));
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

        $sessionMock = $this->getModelMock('adminhtml/session', ['addSuccess']);
        $sessionMock
            ->expects(static::once())
            ->method('addSuccess')
            ->with(static::stringContains('The order address has been updated.'));
        $this->replaceByMock('singleton', 'adminhtml/session', $sessionMock);

        $postData = [
            'telephone' => $telephone,
            'versenden_info' => [
                'street_name' => $streetName,
                'street_number' => $streetNumber,
                'address_addition' => '',
                'packstation' => [
                    'packstation_number' => $facilityNumber,
                ],
                'postfiliale' => [
                    'postfilial_number' => $facilityNumber,
                ],
                'parcelshop' => [
                    'parcelshop_number' => $facilityNumber,
                ],
            ],
        ];
        $this->getRequest()->setPost($postData);
        $this->dispatch('adminhtml/sales_order/addressSave', ['address_id' => 100]);
        $this->assertRedirectTo('adminhtml/sales_order/view', ['order_id' => 10]);
        $this->assertEventDispatched('dhl_versenden_announce_postal_facility');

        $address = Mage::getModel('sales/order_address')->load(100);
        /** @var \Dhl\Versenden\ParcelDe\Info $versendenInfo */
        $versendenInfo = $address->getData('dhl_versenden_info');
        static::assertInstanceOf(\Dhl\Versenden\ParcelDe\Info::class, $versendenInfo);
        static::assertEquals($streetName, $versendenInfo->getReceiver()->streetName);
        static::assertEquals($streetNumber, $versendenInfo->getReceiver()->streetNumber);
        static::assertEmpty($versendenInfo->getReceiver()->packstation->packstationNumber);
        static::assertEmpty($versendenInfo->getReceiver()->postfiliale->postfilialNumber);
        static::assertEmpty($versendenInfo->getReceiver()->parcelShop->parcelShopNumber);
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

        $sessionMock = $this->getModelMock('adminhtml/session', ['addSuccess']);
        $sessionMock
            ->expects(static::once())
            ->method('addSuccess')
            ->with(static::stringContains('The order address has been updated.'));
        $this->replaceByMock('singleton', 'adminhtml/session', $sessionMock);

        $postData = [
            'telephone' => $telephone,
            'versenden_info' => [
                'street_name' => $streetName,
                'street_number' => $streetNumber,
                'address_addition' => '',
                'packstation' => [
                    'packstation_number' => $packStationNumber,
                    'post_number' => $postNumber,
                ],
            ],
        ];
        $this->getRequest()->setPost($postData);
        $this->dispatch('adminhtml/sales_order/addressSave', ['address_id' => 100]);
        $this->assertRedirectTo('adminhtml/sales_order/view', ['order_id' => 10]);
        $this->assertEventDispatched('dhl_versenden_announce_postal_facility');

        $address = Mage::getModel('sales/order_address')->load(100);
        /** @var \Dhl\Versenden\ParcelDe\Info $versendenInfo */
        $versendenInfo = $address->getData('dhl_versenden_info');
        static::assertInstanceOf(\Dhl\Versenden\ParcelDe\Info::class, $versendenInfo);
        static::assertEquals($streetName, $versendenInfo->getReceiver()->streetName);
        static::assertEquals($streetNumber, $versendenInfo->getReceiver()->streetNumber);
        static::assertEquals($packStationNumber, $versendenInfo->getReceiver()->packstation->packstationNumber);
        static::assertEquals($postNumber, $versendenInfo->getReceiver()->packstation->postNumber);
        static::assertEmpty($versendenInfo->getReceiver()->postfiliale->postfilialNumber);
        static::assertEmpty($versendenInfo->getReceiver()->parcelShop->parcelShopNumber);
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

        $sessionMock = $this->getModelMock('adminhtml/session', ['addSuccess']);
        $sessionMock
            ->expects(static::once())
            ->method('addSuccess')
            ->with(static::stringContains('The order address has been updated.'));
        $this->replaceByMock('singleton', 'adminhtml/session', $sessionMock);

        $postData = [
            'telephone' => $telephone,
            'versenden_info' => [
                'street_name' => $streetName,
                'street_number' => $streetNumber,
                'address_addition' => '',
                'postfiliale' => [
                    'postfilial_number' => $postfilialNumber,
                    'post_number' => $postNumber,
                ],
            ],
        ];
        $this->getRequest()->setPost($postData);
        $this->dispatch('adminhtml/sales_order/addressSave', ['address_id' => 100]);
        $this->assertRedirectTo('adminhtml/sales_order/view', ['order_id' => 10]);
        $this->assertEventDispatched('dhl_versenden_announce_postal_facility');

        $address = Mage::getModel('sales/order_address')->load(100);
        /** @var \Dhl\Versenden\ParcelDe\Info $versendenInfo */
        $versendenInfo = $address->getData('dhl_versenden_info');
        static::assertInstanceOf(\Dhl\Versenden\ParcelDe\Info::class, $versendenInfo);
        static::assertEquals($streetName, $versendenInfo->getReceiver()->streetName);
        static::assertEquals($streetNumber, $versendenInfo->getReceiver()->streetNumber);
        static::assertEmpty($versendenInfo->getReceiver()->packstation->packstationNumber);
        static::assertEquals($postfilialNumber, $versendenInfo->getReceiver()->postfiliale->postfilialNumber);
        static::assertEquals($postNumber, $versendenInfo->getReceiver()->postfiliale->postNumber);
        static::assertEmpty($versendenInfo->getReceiver()->parcelShop->parcelShopNumber);
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

        $sessionMock = $this->getModelMock('adminhtml/session', ['addSuccess']);
        $sessionMock
            ->expects(static::once())
            ->method('addSuccess')
            ->with(static::stringContains('The order address has been updated.'));
        $this->replaceByMock('singleton', 'adminhtml/session', $sessionMock);

        $postData = [
            'telephone' => $telephone,
            'versenden_info' => [
                'street_name' => $streetName,
                'street_number' => $streetNumber,
                'address_addition' => '',
                'parcel_shop' => [
                    'parcel_shop_number' => $parcelShopNumber,
                    'street_name' => $streetName,
                    'street_number' => $streetNumber,
                ],
            ],
        ];
        $this->getRequest()->setPost($postData);
        $this->dispatch('adminhtml/sales_order/addressSave', ['address_id' => 100]);
        $this->assertRedirectTo('adminhtml/sales_order/view', ['order_id' => 10]);
        $this->assertEventDispatched('dhl_versenden_announce_postal_facility');

        $address = Mage::getModel('sales/order_address')->load(100);
        /** @var \Dhl\Versenden\ParcelDe\Info $versendenInfo */
        $versendenInfo = $address->getData('dhl_versenden_info');
        static::assertInstanceOf(\Dhl\Versenden\ParcelDe\Info::class, $versendenInfo);
        static::assertEquals($streetName, $versendenInfo->getReceiver()->streetName);
        static::assertEquals($streetNumber, $versendenInfo->getReceiver()->streetNumber);
        static::assertEmpty($versendenInfo->getReceiver()->packstation->packstationNumber);
        static::assertEmpty($versendenInfo->getReceiver()->postfiliale->postfilialNumber);
        static::assertEquals($parcelShopNumber, $versendenInfo->getReceiver()->parcelShop->parcelShopNumber);
        static::assertEquals($streetName, $versendenInfo->getReceiver()->parcelShop->streetName);
        static::assertEquals($streetNumber, $versendenInfo->getReceiver()->parcelShop->streetNumber);
    }
}
