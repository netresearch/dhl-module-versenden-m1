<?php

/**
 * See LICENSE.md for license details.
 */

/**
 * Test Parcel Management API Client
 *
 * Simple functional tests for the Parcel Management client.
 */
class Dhl_Versenden_Test_Model_Webservice_Client_ParcelManagementTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Test client can be instantiated
     *
     * @test
     */
    public function clientCanBeInstantiated()
    {
        $client = Mage::getModel('dhl_versenden/webservice_client_parcelManagement');

        static::assertInstanceOf(
            'Dhl_Versenden_Model_Webservice_Client_ParcelManagement',
            $client,
        );
    }

    /**
     * Test client has the required method
     *
     * @test
     */
    public function clientHasRequiredMethod()
    {
        $client = Mage::getModel('dhl_versenden/webservice_client_parcelManagement');

        static::assertTrue(
            method_exists($client, 'checkoutRecipientZipAvailableServicesGet'),
        );
    }

    /**
     * Test invalid date throws Mage_Core_Exception
     *
     * @test
     */
    public function invalidDateThrowsException()
    {
        $client = Mage::getModel('dhl_versenden/webservice_client_parcelManagement');

        $this->expectException(Mage_Core_Exception::class);
        $this->expectExceptionMessageMatches('/Failed to query available services/');

        $client->checkoutRecipientZipAvailableServicesGet('invalid-date', '10115');
    }

    /**
     * Test empty ZIP throws Mage_Core_Exception
     *
     * @test
     */
    public function emptyZipThrowsException()
    {
        $client = Mage::getModel('dhl_versenden/webservice_client_parcelManagement');

        $this->expectException(Mage_Core_Exception::class);
        $this->expectExceptionMessageMatches('/Failed to query available services/');

        $client->checkoutRecipientZipAvailableServicesGet('2025-11-25 14:30:00', '');
    }
}
