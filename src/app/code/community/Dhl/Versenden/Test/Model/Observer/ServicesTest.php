<?php

/**
 * See LICENSE.md for license details.
 */

/**
 * Test Observer_Services functionality.
 */
class Dhl_Versenden_Test_Model_Observer_ServicesTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Test checkValue allows valid preferred location input.
     *
     * @test
     */
    public function checkValueAllowsValidLocation()
    {
        $observer = new Dhl_Versenden_Model_Observer_Services();

        // Valid inputs should not throw
        $observer->checkValue('Garage', 'preferredLocation');
        $observer->checkValue('Behind the house', 'preferredLocation');
        $observer->checkValue('Next to the door', 'preferredLocation');

        static::assertTrue(true);
    }

    /**
     * Test checkValue allows valid preferred neighbour input.
     *
     * @test
     */
    public function checkValueAllowsValidNeighbour()
    {
        $observer = new Dhl_Versenden_Model_Observer_Services();

        // Valid inputs should not throw
        $observer->checkValue('Mr Smith', 'preferredNeighbour');
        $observer->checkValue('Apartment 5B', 'preferredNeighbour');

        static::assertTrue(true);
    }

    /**
     * Test checkValue rejects Packstation reference.
     *
     * @test
     */
    public function checkValueRejectsPackstation()
    {
        $this->expectException('Mage_Core_Exception');
        $this->expectExceptionMessage('invalid');

        $observer = new Dhl_Versenden_Model_Observer_Services();
        $observer->checkValue('Packstation 123', 'preferredLocation');
    }

    /**
     * Test checkValue rejects Paketbox reference.
     *
     * @test
     */
    public function checkValueRejectsPaketbox()
    {
        $this->expectException('Mage_Core_Exception');
        $this->expectExceptionMessage('invalid');

        $observer = new Dhl_Versenden_Model_Observer_Services();
        $observer->checkValue('Paketbox 456', 'preferredLocation');
    }

    /**
     * Test checkValue rejects Postfiliale reference.
     *
     * @test
     */
    public function checkValueRejectsPostfiliale()
    {
        $this->expectException('Mage_Core_Exception');
        $this->expectExceptionMessage('invalid');

        $observer = new Dhl_Versenden_Model_Observer_Services();
        $observer->checkValue('Postfiliale 789', 'preferredLocation');
    }

    /**
     * Test checkValue rejects DHL keyword.
     *
     * @test
     */
    public function checkValueRejectsDhlKeyword()
    {
        $this->expectException('Mage_Core_Exception');
        $this->expectExceptionMessage('invalid');

        $observer = new Dhl_Versenden_Model_Observer_Services();
        $observer->checkValue('DHL Point', 'preferredNeighbour');
    }

    /**
     * Test checkValue rejects special characters.
     *
     * @test
     */
    public function checkValueRejectsSpecialCharacters()
    {
        $this->expectException('Mage_Core_Exception');
        $this->expectExceptionMessage('invalid');

        $observer = new Dhl_Versenden_Model_Observer_Services();
        $observer->checkValue('Location + more', 'preferredLocation');
    }

    /**
     * Test checkValue rejects quote characters.
     *
     * @test
     */
    public function checkValueRejectsQuoteCharacters()
    {
        $this->expectException('Mage_Core_Exception');
        $this->expectExceptionMessage('invalid');

        $observer = new Dhl_Versenden_Model_Observer_Services();
        $observer->checkValue("Test's location", 'preferredLocation');
    }

    /**
     * Test checkValue rejects bracket characters.
     *
     * @test
     */
    public function checkValueRejectsBracketCharacters()
    {
        $this->expectException('Mage_Core_Exception');
        $this->expectExceptionMessage('invalid');

        $observer = new Dhl_Versenden_Model_Observer_Services();
        $observer->checkValue('Location [test]', 'preferredLocation');
    }

    /**
     * Test validateLocationDetails processes shipment requests.
     *
     * @test
     */
    public function validateLocationDetailsProcessesRequests()
    {
        $observer = new Dhl_Versenden_Model_Observer_Services();

        // Create shipment request with valid services
        $request = new Mage_Shipping_Model_Shipment_Request();
        $request->setData('services', [
            'service_setting' => [
                \Dhl\Versenden\ParcelDe\Service\PreferredLocation::CODE => 'Garage',
                \Dhl\Versenden\ParcelDe\Service\PreferredNeighbour::CODE => 'Mr Smith',
            ],
        ]);

        $event = new Varien_Event();
        $event->setData('shipment_requests', [$request]);

        $eventObserver = new Varien_Event_Observer();
        $eventObserver->setEvent($event);

        // Should not throw for valid input
        $observer->validateLocationDetails($eventObserver);

        static::assertTrue(true);
    }

    /**
     * Test validateLocationDetails skips when no services.
     *
     * @test
     */
    public function validateLocationDetailsSkipsWhenNoServices()
    {
        $observer = new Dhl_Versenden_Model_Observer_Services();

        // Create shipment request without services
        $request = new Mage_Shipping_Model_Shipment_Request();

        $event = new Varien_Event();
        $event->setData('shipment_requests', [$request]);

        $eventObserver = new Varien_Event_Observer();
        $eventObserver->setEvent($event);

        // Should not throw
        $observer->validateLocationDetails($eventObserver);

        static::assertTrue(true);
    }

    /**
     * Test validateLocationDetails skips when no service_setting.
     *
     * @test
     */
    public function validateLocationDetailsSkipsWhenNoServiceSetting()
    {
        $observer = new Dhl_Versenden_Model_Observer_Services();

        // Create shipment request with services but no service_setting
        $request = new Mage_Shipping_Model_Shipment_Request();
        $request->setData('services', ['some_other_key' => 'value']);

        $event = new Varien_Event();
        $event->setData('shipment_requests', [$request]);

        $eventObserver = new Varien_Event_Observer();
        $eventObserver->setEvent($event);

        // Should not throw
        $observer->validateLocationDetails($eventObserver);

        static::assertTrue(true);
    }

    /**
     * Test validateLocationDetails throws for invalid preferred location.
     *
     * @test
     */
    public function validateLocationDetailsThrowsForInvalidLocation()
    {
        $this->expectException('Mage_Core_Exception');
        $this->expectExceptionMessage('invalid');

        $observer = new Dhl_Versenden_Model_Observer_Services();

        // Create shipment request with invalid preferred location
        $request = new Mage_Shipping_Model_Shipment_Request();
        $request->setData('services', [
            'service_setting' => [
                \Dhl\Versenden\ParcelDe\Service\PreferredLocation::CODE => 'Packstation 123',
            ],
        ]);

        $event = new Varien_Event();
        $event->setData('shipment_requests', [$request]);

        $eventObserver = new Varien_Event_Observer();
        $eventObserver->setEvent($event);

        $observer->validateLocationDetails($eventObserver);
    }

    /**
     * Test validateLocationDetails throws for invalid preferred neighbour.
     *
     * @test
     */
    public function validateLocationDetailsThrowsForInvalidNeighbour()
    {
        $this->expectException('Mage_Core_Exception');
        $this->expectExceptionMessage('invalid');

        $observer = new Dhl_Versenden_Model_Observer_Services();

        // Create shipment request with invalid preferred neighbour
        $request = new Mage_Shipping_Model_Shipment_Request();
        $request->setData('services', [
            'service_setting' => [
                \Dhl\Versenden\ParcelDe\Service\PreferredNeighbour::CODE => 'DHL Shop',
            ],
        ]);

        $event = new Varien_Event();
        $event->setData('shipment_requests', [$request]);

        $eventObserver = new Varien_Event_Observer();
        $eventObserver->setEvent($event);

        $observer->validateLocationDetails($eventObserver);
    }
}
