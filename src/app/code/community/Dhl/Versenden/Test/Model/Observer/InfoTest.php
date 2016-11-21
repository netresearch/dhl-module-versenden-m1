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
 * Dhl_Versenden_Test_Model_Observer_InfoTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Test_Model_Observer_InfoTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @return \Netresearch\Dhl\Versenden\Info
     */
    protected function prepareVersendenInfo()
    {
        $streetName = 'Street Name';
        $streetNumber = '127';

        $versendenInfo = new \Netresearch\Dhl\Versenden\Info();
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
        $resourceMock = $this->getResourceModelMock('sales/quote_address', array('save'));
        $this->replaceByMock('resource_model', 'sales/quote_address', $resourceMock);

        $versendenInfo = $this->prepareVersendenInfo();
        $address = Mage::getModel('sales/quote_address')->load(100);
        $address->setData('dhl_versenden_info', $versendenInfo);

        $this->assertSame($versendenInfo, $address->getData('dhl_versenden_info'));
        $address->save();
        $this->assertInstanceOf(Netresearch\Dhl\Versenden\Info::class, $address->getData('dhl_versenden_info'));
        $this->assertNotSame($versendenInfo, $address->getData('dhl_versenden_info'));
        $this->assertEquals(
            $versendenInfo->getReceiver()->streetName,
            $address->getData('dhl_versenden_info')->getReceiver()->streetName
        );
        $this->assertEquals(
            $versendenInfo->getReceiver()->streetNumber,
            $address->getData('dhl_versenden_info')->getReceiver()->streetNumber
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
            ->setMethods(array('getData', 'setData'))
            ->getMock();
        $address
            ->expects($this->never())
            ->method('getData')
            ->with($this->equalTo('dhl_versenden_info'), $this->equalTo(null));
        $address
            ->expects($this->never())
            ->method('setData')
            ->with($this->equalTo('dhl_versenden_info'), $this->anything());
        $observer->setData('object', $address);

        $dhlObserver = new Dhl_Versenden_Model_Observer();
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
        $addressMock = $this->getModelMock('sales/quote_address', array('getData', 'setData'));
        $addressMock
            ->expects($this->once())
            ->method('getData')
            ->with($this->equalTo('dhl_versenden_info'), $this->equalTo(null))
            ->willReturn($versendenInfo);
        $addressMock
            ->expects($this->never())
            ->method('setData')
            ->with($this->equalTo('dhl_versenden_info'), $this->anything());
        $this->replaceByMock('model', 'sales/quote_address', $addressMock);

        $address = Mage::getModel('sales/quote_address');
        $observer->setData('object', $address);

        $dhlObserver = new Dhl_Versenden_Model_Observer();
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

        $this->assertInstanceOf(\Netresearch\Dhl\Versenden\Info::class, $address->getData('dhl_versenden_info'));
        $dhlObserver = new Dhl_Versenden_Model_Observer();
        $dhlObserver->serializeVersendenInfo($observer);
        $this->assertJson($address->getData('dhl_versenden_info'));
    }

    /**
     * @test
     * @loadFixture Model_ObserverTest
     */
    public function unserializeVersendenInfoItemsWrongAddressType()
    {
        $observer = new Varien_Event_Observer();

        $addressCollection = $this->getMockBuilder(Varien_Object::class)
            ->setMethods(array('walk'))
            ->getMock();
        $addressCollection
            ->expects($this->never())
            ->method('walk');
        $observer->setData('object', $addressCollection);

        $dhlObserver = new Dhl_Versenden_Model_Observer();
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
        $addressCollection = $this->getResourceModelMock('sales/quote_address_collection', array('walk'));
        $addressCollection
            ->expects($this->never())
            ->method('walk');
        $observer->setData('object', $addressCollection);

        $address = Mage::getModel('sales/quote_address')->load(100);
        $address->setData('dhl_versenden_info', $versendenInfo);
        /** @var Mage_Sales_Model_Resource_Quote_Address_Collection $addressCollection */
        $addressCollection->addItem($address);

        $dhlObserver = new Dhl_Versenden_Model_Observer();
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
        $dhlObserver = new Dhl_Versenden_Model_Observer();
        $dhlObserver->unserializeVersendenInfoItems($observer);
        $this->assertInstanceOf(\Netresearch\Dhl\Versenden\Info::class, $address->getData('dhl_versenden_info'));
    }
}
