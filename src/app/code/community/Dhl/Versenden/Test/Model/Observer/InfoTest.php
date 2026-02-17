<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Test_Model_Observer_InfoTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @return \Dhl\Versenden\ParcelDe\Info
     */
    protected function prepareVersendenInfo()
    {
        $streetName = 'Street Name';
        $streetNumber = '127';

        $versendenInfo = new \Dhl\Versenden\ParcelDe\Info();
        $versendenInfo->getReceiver()->streetName = $streetName;
        $versendenInfo->getReceiver()->streetNumber = $streetNumber;

        return $versendenInfo;
    }

    /**
     * @test
     * @loadFixture Model_ObserverTest
     */
    public function saveVersendenInfo()
    {
        $observer = new Varien_Event_Observer();

        $versendenInfo = $this->prepareVersendenInfo();
        $address = Mage::getModel('sales/quote_address')->load(100);
        $address->setData('dhl_versenden_info', $versendenInfo);

        static::assertInstanceOf(\Dhl\Versenden\ParcelDe\Info::class, $address->getData('dhl_versenden_info'));

        // Manually trigger serialize observer (simulating model_save_before)
        $observer->setData('object', $address);
        $dhlObserver = new Dhl_Versenden_Model_Observer_Serialize();
        $dhlObserver->serializeVersendenInfo($observer);

        // Verify serialization worked
        static::assertIsString($address->getData('dhl_versenden_info'));
        $serializedData = $address->getData('dhl_versenden_info');
        static::assertNotNull(json_decode($serializedData), 'Data should be valid JSON');

        // Manually trigger unserialize observer (simulating model_save_after)
        $dhlObserver->unserializeVersendenInfo($observer);

        // Verify unserialization worked
        static::assertInstanceOf(\Dhl\Versenden\ParcelDe\Info::class, $address->getData('dhl_versenden_info'));
        static::assertNotSame($versendenInfo, $address->getData('dhl_versenden_info'));
        static::assertEquals(
            $versendenInfo->getReceiver()->streetName,
            $address->getData('dhl_versenden_info')->getReceiver()->streetName,
        );
        static::assertEquals(
            $versendenInfo->getReceiver()->streetNumber,
            $address->getData('dhl_versenden_info')->getReceiver()->streetNumber,
        );
    }

    /**
     * @test
     * @loadFixture Model_ObserverTest
     */
    public function serializeVersendenInfoWrongAddressType()
    {
        $observer = new Varien_Event_Observer();

        $address = $this->getMockBuilder(Varien_Object::class)
            ->setMethods(['getData', 'setData'])
            ->getMock();
        $address
            ->expects(static::never())
            ->method('getData')
            ->with(static::equalTo('dhl_versenden_info'), static::equalTo(null));
        $address
            ->expects(static::never())
            ->method('setData')
            ->with(static::equalTo('dhl_versenden_info'), static::anything());
        $observer->setData('object', $address);

        $dhlObserver = new Dhl_Versenden_Model_Observer_Serialize();
        $dhlObserver->serializeVersendenInfo($observer);
    }

    /**
     * @test
     * @loadFixture Model_ObserverTest
     */
    public function serializeVersendenInfoWrongInfoType()
    {
        $observer = new Varien_Event_Observer();

        $versendenInfo = new Varien_Object();
        $addressMock = $this->getModelMock('sales/quote_address', ['getData', 'setData']);
        $addressMock
            ->expects(static::once())
            ->method('getData')
            ->with(static::equalTo('dhl_versenden_info'), static::equalTo(null))
            ->willReturn($versendenInfo);
        $addressMock
            ->expects(static::never())
            ->method('setData')
            ->with(static::equalTo('dhl_versenden_info'), static::anything());
        $this->replaceByMock('model', 'sales/quote_address', $addressMock);

        $address = Mage::getModel('sales/quote_address');
        $observer->setData('object', $address);

        $dhlObserver = new Dhl_Versenden_Model_Observer_Serialize();
        $dhlObserver->serializeVersendenInfo($observer);
    }

    /**
     * @test
     * @loadFixture Model_ObserverTest
     */
    public function serializeVersendenInfoOk()
    {
        $observer = new Varien_Event_Observer();

        $versendenInfo = $this->prepareVersendenInfo();
        $address = Mage::getModel('sales/quote_address');
        $address->setData('dhl_versenden_info', $versendenInfo);
        $observer->setData('object', $address);

        static::assertInstanceOf(\Dhl\Versenden\ParcelDe\Info::class, $address->getData('dhl_versenden_info'));
        $dhlObserver = new Dhl_Versenden_Model_Observer_Serialize();
        $dhlObserver->serializeVersendenInfo($observer);
        $serializedData = $address->getData('dhl_versenden_info');
        static::assertIsString($serializedData);
        static::assertNotNull(json_decode($serializedData), 'Data should be valid JSON');
    }

    /**
     * @test
     * @loadFixture Model_ObserverTest
     */
    public function unserializeVersendenInfoItemsWrongAddressType()
    {
        $observer = new Varien_Event_Observer();

        $addressCollection = $this->getMockBuilder(Varien_Object::class)
            ->setMethods(['walk'])
            ->getMock();
        $addressCollection
            ->expects(static::never())
            ->method('walk');
        $observer->setData('object', $addressCollection);

        $dhlObserver = new Dhl_Versenden_Model_Observer_Serialize();
        $dhlObserver->unserializeVersendenInfoItems($observer);
    }

    /**
     * @test
     * @loadFixture Model_ObserverTest
     */
    public function unserializeVersendenInfoItemsWrongType()
    {
        $observer = new Varien_Event_Observer();

        $versendenInfo = new Varien_Object();
        $addressCollection = $this->getResourceModelMock('sales/quote_address_collection', ['walk']);
        $addressCollection
            ->expects(static::never())
            ->method('walk');
        $observer->setData('object', $addressCollection);

        $address = Mage::getModel('sales/quote_address')->load(100);
        $address->setData('dhl_versenden_info', $versendenInfo);
        /** @var Mage_Sales_Model_Resource_Quote_Address_Collection $addressCollection */
        $addressCollection->addItem($address);

        $dhlObserver = new Dhl_Versenden_Model_Observer_Serialize();
        $dhlObserver->unserializeVersendenInfoItems($observer);
    }

    /**
     * @test
     * @loadFixture Model_ObserverTest
     */
    public function unserializeVersendenInfoItemsOk()
    {
        $observer = new Varien_Event_Observer();

        $versendenInfo = '{"schemaVersion":"1.0"}';

        // load collection and remove all items while retaining "collection_loaded" status
        /** @var Mage_Sales_Model_Resource_Quote_Address_Collection $collection */
        $collection = Mage::getResourceModel('sales/quote_address_collection')->load();
        $addressIds = $collection->getColumnValues('address_id');
        foreach ($addressIds as $addressId) {
            $collection->removeItemByKey($addressId);
        }

        // add address with versenden info json to collection
        $address = Mage::getModel('sales/quote_address')->load(100);
        $address->setData('dhl_versenden_info', $versendenInfo);
        $collection->addItem($address);

        $observer->setData('order_address_collection', $collection);
        $dhlObserver = new Dhl_Versenden_Model_Observer_Serialize();
        $dhlObserver->unserializeVersendenInfoItems($observer);
        static::assertInstanceOf(\Dhl\Versenden\ParcelDe\Info::class, $address->getData('dhl_versenden_info'));
    }
}
