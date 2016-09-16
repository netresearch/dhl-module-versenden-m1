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
use \Dhl\Versenden\Info\Receiver\PostalFacility;
/**
 * Dhl_Versenden_Test_Model_Observer_PostalFacilityTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Test_Model_Observer_PostalFacilityTest
    extends EcomDev_PHPUnit_Test_Case
{
    protected function getLocationTypes()
    {
        return array(
            PostalFacility::TYPE_PACKSTATION => 'Packstation',
            PostalFacility::TYPE_POSTFILIALE => 'Postfiliale',
            PostalFacility::TYPE_PAKETSHOP => 'Paketshop',
        );
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
            $stationId
        );
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
        $stationTypes = $this->getLocationTypes();
        $stationType = PostalFacility::TYPE_POSTFILIALE;

        $stationId   = '123';
        // valid shop, recognized type
        $street = sprintf(
            '%s %s',
            $stationTypes[$stationType],
            $stationId
        );
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
        $stationTypes = $this->getLocationTypes();
        $stationType = PostalFacility::TYPE_PAKETSHOP;

        $stationId   = '123';
        // valid shop, but unrecognized type
        $street = sprintf(
            '%s %s',
            $stationTypes[$stationType],
            $stationId
        );
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
            array('getQuote'),
            false,
            array(),
            '',
            false
        );
        $sessionMock
            ->expects($this->never())
            ->method('getQuote');
        $this->replaceByMock('singleton', 'checkout/session', $sessionMock);

        $dhlObserver = new Dhl_Versenden_Model_Observer();
        $dhlObserver->disableCodPayment($observer);
    }
}
