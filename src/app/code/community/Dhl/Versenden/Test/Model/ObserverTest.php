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
 * Dhl_Versenden_Test_Model_ObserverTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Test_Model_ObserverTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     */
    public function registerAutoload()
    {
        $configMock = $this->getModelMock('dhl_versenden/config', array('isAutoloadEnabled'));
        $configMock
            ->expects($this->once())
            ->method('isAutoloadEnabled')
            ->willReturn(true);
        $this->replaceByMock('model', 'dhl_versenden/config', $configMock);

        $autoloaderMock = $this->getHelperMock('dhl_versenden/autoloader', array('register'));
        $autoloaderMock
            ->expects($this->once())
            ->method('register');
        $this->replaceByMock('helper', 'dhl_versenden/autoloader', $autoloaderMock);

        $observer = new Dhl_Versenden_Model_Observer();
        $observer->registerAutoload();
    }

    /**
     * @test
     */
    public function registerAutoloadOff()
    {
        $configMock = $this->getModelMock('dhl_versenden/config', array('isAutoloadEnabled'));
        $configMock
            ->expects($this->once())
            ->method('isAutoloadEnabled')
            ->willReturn(false);
        $this->replaceByMock('model', 'dhl_versenden/config', $configMock);

        $autoloaderMock = $this->getHelperMock('dhl_versenden/autoloader', array('register'));
        $autoloaderMock
            ->expects($this->never())
            ->method('register');
        $this->replaceByMock('helper', 'dhl_versenden/autoloader', $autoloaderMock);

        $observer = new Dhl_Versenden_Model_Observer();
        $observer->registerAutoload();
    }

    /**
     * @test
     * @loadFixture ../../ConfigTest/fixtures/ConfigTest
     */
    public function appendServices()
    {
        $this->setCurrentStore('store_two');

        $observer = new Varien_Event_Observer();
        $block = new Mage_Checkout_Block_Onepage_Shipping_Method_Available();
        $blockHtml = '<span>foo</span>';
        $transport = new Varien_Object();
        $transport->setHtml($blockHtml);

        $observer->setBlock($block);
        $observer->setTransport($transport);

        $dhlObserver = new Dhl_Versenden_Model_Observer();
        $dhlObserver->appendServices($observer);

        $this->assertStringStartsWith($blockHtml, $transport->getHtml());
        $this->assertContains('checkout-dhlversenden-services', $transport->getHtml());
    }

    /**
     * Assert early return.
     *
     * @test
     * @loadFixture ../../ConfigTest/fixtures/ConfigTest
     */
    public function appendNoServices()
    {
        $this->setCurrentStore('store_two');

        $observerMock = $this->getMockBuilder(Varien_Event_Observer::class)
            ->setMethods(array('getTransport'))
            ->getMock();
        $observerMock
            ->expects($this->never())
            ->method('getTransport');

        $block = new Mage_Core_Block_Text();
        $observerMock->setBlock($block);

        $dhlObserver = new Dhl_Versenden_Model_Observer();
        $dhlObserver->appendServices($observerMock);
    }

    /**
     * @test
     * @loadFixture Quotes
     */
    public function saveShippingSettings()
    {
        $this->setCurrentStore('store_two');

        // SERVICE DEFINITION
        $preferredLocationValue = 'Garage Location';
        $preferredLocation = new \Dhl\Versenden\Service\PreferredLocation($preferredLocationValue);

        $preferredNeighbourValue = 'Foo Neighbour';
        $preferredNeighbour = new \Dhl\Versenden\Service\PreferredNeighbour($preferredNeighbourValue);

        // two settings, only one actually enabled
        $requestMock = $this->getMockBuilder(Mage_Core_Controller_Request_Http::class)
            ->setMethods(array('getPost'))
            ->getMock();
        $requestMock
            ->expects($this->exactly(2))
            ->method('getPost')
            ->withConsecutive($this->equalTo('shipment_service'), $this->equalTo('service_setting'))
            ->willReturnMap(
                array(
                    array('shipment_service', array(), array(
                        $preferredLocation->getCode() => $preferredLocation->getCode()
                    )),
                    array('service_setting', array(), array(
                        $preferredLocation->getCode() => $preferredLocation->value,
                        $preferredNeighbour->getCode() => $preferredNeighbour->value,
                    ))
                )
            );

        $observerMock = $this->getMockBuilder(Varien_Event_Observer::class)
            ->setMethods(array('getRequest'))
            ->getMock();
        $observerMock
            ->expects($this->once())
            ->method('getRequest')
            ->willReturn($requestMock);

        // ADDRESS DEFINITION
        $addressCompany = 'Dhl Foo Company';

        $quote = Mage::getModel('sales/quote')->load(200);
        $quote->getShippingAddress()->setCompany($addressCompany);
        $observerMock->setQuote($quote);

        $dhlObserver = new Dhl_Versenden_Model_Observer();
        $dhlObserver->saveShippingSettings($observerMock);

        $versendenInfo = $quote->getShippingAddress()->getDhlVersendenInfo();
        $this->assertNotEmpty($versendenInfo);
        $this->assertContains($preferredLocationValue, $versendenInfo);
        $this->assertNotContains($preferredNeighbourValue, $versendenInfo);
        $this->assertContains($addressCompany, $versendenInfo);
    }

    /**
     * Assert early return (wrong shipping method).
     *
     * @test
     * @loadFixture Quotes
     */
    public function saveNoShippingSettings()
    {
        $this->setCurrentStore('store_one');
        $quote = Mage::getModel('sales/quote')->load(100);

        $observerMock = $this->getMockBuilder(Varien_Event_Observer::class)
            ->setMethods(array('getRequest'))
            ->getMock();
        $observerMock
            ->expects($this->never())
            ->method('getRequest');
        $observerMock->setQuote($quote);

        $dhlObserver = new Dhl_Versenden_Model_Observer();
        $dhlObserver->saveShippingSettings($observerMock);
    }

    /**
     * @test
     */
    public function updateCarrier()
    {
        $fooCarrier = 'foo';
        $dhlCarrier = Dhl_Versenden_Model_Shipping_Carrier_Versenden::CODE;
        $method     = 'bar';

        $observer = new Varien_Event_Observer();
        $order = new Varien_Object();
        $order->setShippingMethod("{$fooCarrier}_{$method}");
        $observer->setOrder($order);

        $configMock = $this->getModelMock('dhl_versenden/config', array('canProcessMethod'));
        $configMock
            ->expects($this->any())
            ->method('canProcessMethod')
            ->willReturnOnConsecutiveCalls(false, true);
        $this->replaceByMock('model', 'dhl_versenden/config', $configMock);

        $dhlObserver = new Dhl_Versenden_Model_Observer();

        $dhlObserver->updateCarrier($observer);
        $this->assertEquals("{$fooCarrier}_{$method}", $observer->getOrder()->getShippingMethod());

        $dhlObserver->updateCarrier($observer);
        $this->assertEquals("{$dhlCarrier}_{$method}", $observer->getOrder()->getShippingMethod());
    }

    /**
     * @test
     */
    public function preparePackstation()
    {
        $stationType = \Dhl\Versenden\ShippingInfo\PostalFacility::TYPE_PACKSTATION;
        $stationId   = '987';

        $street = "{$stationType} {$stationId}"; // valid shop, recognized type
        $company = '1234567890'; // valid post number

        $postalFacility = new Varien_Object();
        $address = new Varien_Object(array(
            'street_full' => $street,
            'company'     => $company,
        ));

        $observer = new Varien_Event_Observer();
        $observer->setData(array(
            'postal_facility' => $postalFacility,
            'quote_address' => $address,
        ));

        $dhlObserver = new Dhl_Versenden_Model_Observer();
        $dhlObserver->preparePostalFacility($observer);

        $this->assertEquals($stationType, $postalFacility->getData('shop_type'));
        $this->assertEquals($stationId, $postalFacility->getData('shop_number'));
        $this->assertEquals($company, $postalFacility->getData('post_number'));
    }

    /**
     * @test
     */
    public function preparePostfiliale()
    {
        $stationType = \Dhl\Versenden\ShippingInfo\PostalFacility::TYPE_POSTFILIALE;
        $stationId   = '123';

        $street = "{$stationType} {$stationId}"; // valid shop, recognized type
        $company = '1234567890'; // valid post number

        $postalFacility = new Varien_Object();
        $address = new Varien_Object(array(
            'street_full' => $street,
            'company'     => $company,
        ));

        $observer = new Varien_Event_Observer();
        $observer->setData(array(
            'postal_facility' => $postalFacility,
            'quote_address' => $address,
        ));

        $dhlObserver = new Dhl_Versenden_Model_Observer();
        $dhlObserver->preparePostalFacility($observer);

        $this->assertEquals($stationType, $postalFacility->getData('shop_type'));
        $this->assertEquals($stationId, $postalFacility->getData('shop_number'));
        $this->assertEquals($company, $postalFacility->getData('post_number'));
    }

    /**
     * @test
     */
    public function preparePostalFacilityWrongType()
    {
        $street = 'ParcelShop 123'; // valid shop, but unrecognized type
        $company = '1234567890'; // valid post number

        $postalFacility = new Varien_Object();
        $address = new Varien_Object(array(
            'street_full' => $street,
            'company'     => $company,
        ));

        $observer = new Varien_Event_Observer();
        $observer->setData(array(
            'postal_facility' => $postalFacility,
            'quote_address' => $address,
        ));

        $dhlObserver = new Dhl_Versenden_Model_Observer();
        $dhlObserver->preparePostalFacility($observer);

        $this->assertFalse($observer->getPostalFacility()->hasData());
    }

    /**
     * @test
     */
    public function preparePostalFacilityMissingPostNumber()
    {
        $street = 'Packstation 123';
        $company = 'DHL'; // invalid post number

        $postalFacility = new Varien_Object();
        $address = new Varien_Object(array(
            'street_full' => $street,
            'company'     => $company,
        ));

        $observer = new Varien_Event_Observer();
        $observer->setData(array(
            'postal_facility' => $postalFacility,
            'quote_address' => $address,
        ));

        $dhlObserver = new Dhl_Versenden_Model_Observer();
        $dhlObserver->preparePostalFacility($observer);

        $this->assertFalse($observer->getPostalFacility()->hasData());
    }

    /**
     * @test
     */
    public function passThroughPostalFacility()
    {
        $thirdPartyData = array(
            'foo' => 'bar'
        );
        $postalFacility = new Varien_Object($thirdPartyData);

        $observer = new Varien_Event_Observer();
        $observer->setData('postal_facility', $postalFacility);

        $dhlObserver = new Dhl_Versenden_Model_Observer();
        $dhlObserver->preparePostalFacility($observer);

        $this->assertSame($thirdPartyData, $observer->getPostalFacility()->getData());
    }
}
