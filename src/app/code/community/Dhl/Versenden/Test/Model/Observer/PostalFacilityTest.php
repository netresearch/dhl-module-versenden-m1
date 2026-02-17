<?php

/**
 * See LICENSE.md for license details.
 */

use Dhl\Versenden\ParcelDe\Info\Receiver\PostalFacility;

class Dhl_Versenden_Test_Model_Observer_PostalFacilityTest extends EcomDev_PHPUnit_Test_Case
{
    protected function getLocationTypes()
    {
        return [
            PostalFacility::TYPE_PACKSTATION => 'Packstation',
            PostalFacility::TYPE_POSTFILIALE => 'Postfiliale',
            PostalFacility::TYPE_PAKETSHOP => 'Paketshop',
        ];
    }

    /**
     * @test
     */
    public function preparePackstation()
    {
        $stationTypes = $this->getLocationTypes();
        $stationType  = PostalFacility::TYPE_PACKSTATION;

        $stationId = '987';
        // valid shop, recognized type:
        $street = sprintf(
            '%s %s',
            $stationTypes[$stationType],
            $stationId,
        );
        $company = '1234567890'; // valid post number

        $postalFacility = new Varien_Object();
        $address = new Varien_Object([
            'street_full' => $street,
            'company'     => $company,
        ]);

        $observer = new Varien_Event_Observer();
        $observer->setData([
            'postal_facility' => $postalFacility,
            'customer_address' => $address,
        ]);

        $dhlObserver = new Dhl_Versenden_Model_Observer();
        $dhlObserver->preparePostalFacility($observer);

        static::assertEquals($stationType, $postalFacility->getData('shop_type'));
        static::assertEquals($stationId, $postalFacility->getData('shop_number'));
        static::assertEquals($company, $postalFacility->getData('post_number'));
    }

    /**
     * @test
     */
    public function preparePostfiliale()
    {
        $stationTypes = $this->getLocationTypes();
        $stationType = PostalFacility::TYPE_POSTFILIALE;

        $stationId   = '123';
        // valid shop, recognized type
        $street = sprintf(
            '%s %s',
            $stationTypes[$stationType],
            $stationId,
        );
        $company = '1234567890'; // valid post number

        $postalFacility = new Varien_Object();
        $address = new Varien_Object([
            'street_full' => $street,
            'company'     => $company,
        ]);

        $observer = new Varien_Event_Observer();
        $observer->setData([
            'postal_facility' => $postalFacility,
            'customer_address' => $address,
        ]);

        $dhlObserver = new Dhl_Versenden_Model_Observer();
        $dhlObserver->preparePostalFacility($observer);

        static::assertEquals($stationType, $postalFacility->getData('shop_type'));
        static::assertEquals($stationId, $postalFacility->getData('shop_number'));
        static::assertEquals($company, $postalFacility->getData('post_number'));
    }

    /**
     * @test
     */
    public function preparePostalFacilityWrongType()
    {
        $stationTypes = $this->getLocationTypes();
        $stationType = PostalFacility::TYPE_PAKETSHOP;

        $stationId   = '123';
        // valid shop, but unrecognized type
        $street = sprintf(
            '%s %s',
            $stationTypes[$stationType],
            $stationId,
        );
        $company = '1234567890'; // valid post number

        $postalFacility = new Varien_Object();
        $address = new Varien_Object([
            'street_full' => $street,
            'company'     => $company,
        ]);

        $observer = new Varien_Event_Observer();
        $observer->setData([
            'postal_facility' => $postalFacility,
            'customer_address' => $address,
        ]);

        $dhlObserver = new Dhl_Versenden_Model_Observer();
        $dhlObserver->preparePostalFacility($observer);

        static::assertFalse($observer->getPostalFacility()->hasData());
    }

    /**
     * @test
     */
    public function preparePostalFacilityMissingPostNumber()
    {
        $street = 'Packstation 123';
        $company = 'DHL'; // invalid post number

        $postalFacility = new Varien_Object();
        $address = new Varien_Object([
            'street_full' => $street,
            'company'     => $company,
        ]);

        $observer = new Varien_Event_Observer();
        $observer->setData([
            'postal_facility' => $postalFacility,
            'customer_address' => $address,
        ]);

        $dhlObserver = new Dhl_Versenden_Model_Observer();
        $dhlObserver->preparePostalFacility($observer);

        static::assertFalse($observer->getPostalFacility()->hasData());
    }

    /**
     * @test
     */
    public function passThroughPostalFacility()
    {
        $thirdPartyData = [
            'foo' => 'bar',
        ];
        $postalFacility = new Varien_Object($thirdPartyData);

        $observer = new Varien_Event_Observer();
        $observer->setData('postal_facility', $postalFacility);

        $dhlObserver = new Dhl_Versenden_Model_Observer();
        $dhlObserver->preparePostalFacility($observer);

        static::assertSame($thirdPartyData, $observer->getPostalFacility()->getData());
    }

    /**
     * @test
     */
    public function disableCodPaymentNotAvailable()
    {
        $checkResult = new stdClass();
        $checkResult->isAvailable = false;

        $observer = new Varien_Event_Observer();
        $observer->setData('result', $checkResult);

        $sessionMock = $this->getModelMock(
            'checkout/session',
            ['getQuote'],
            false,
            [],
            '',
            false,
        );
        $sessionMock
            ->expects(static::never())
            ->method('getQuote');
        $this->replaceByMock('singleton', 'checkout/session', $sessionMock);

        $dhlObserver = new Dhl_Versenden_Model_Observer();
        $dhlObserver->disableCodPayment($observer);
    }
}
