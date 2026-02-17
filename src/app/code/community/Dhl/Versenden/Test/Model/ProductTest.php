<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Test_Model_ProductTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     */
    public function getCodesReturnsAllProductCodes()
    {
        $codes = \Dhl\Versenden\ParcelDe\Product::getCodes();

        static::assertCount(7, $codes);
        static::assertContains('V01PAK', $codes);
        static::assertContains('V62KP', $codes);
        static::assertContains('V53WPAK', $codes);
        static::assertContains('V54EPAK', $codes);
        static::assertContains('V66WPI', $codes);
        static::assertContains('V06TG', $codes);
        static::assertContains('V06WZ', $codes);
    }

    /**
     * @test
     */
    public function getProcedureReturnsCorrectProcedureNumber()
    {
        static::assertEquals('01', \Dhl\Versenden\ParcelDe\Product::getProcedure('V01PAK'));
        static::assertEquals('62', \Dhl\Versenden\ParcelDe\Product::getProcedure('V62KP'));
        static::assertEquals('53', \Dhl\Versenden\ParcelDe\Product::getProcedure('V53WPAK'));
        static::assertEquals('54', \Dhl\Versenden\ParcelDe\Product::getProcedure('V54EPAK'));
        static::assertEquals('66', \Dhl\Versenden\ParcelDe\Product::getProcedure('V66WPI'));
        static::assertEquals('01', \Dhl\Versenden\ParcelDe\Product::getProcedure('V06TG'));
        static::assertEquals('01', \Dhl\Versenden\ParcelDe\Product::getProcedure('V06WZ'));
    }

    /**
     * @test
     */
    public function getProcedureReturnsEmptyStringForInvalidCode()
    {
        static::assertEquals('', \Dhl\Versenden\ParcelDe\Product::getProcedure('INVALID'));
        static::assertEquals('', \Dhl\Versenden\ParcelDe\Product::getProcedure(''));
        static::assertEquals('', \Dhl\Versenden\ParcelDe\Product::getProcedure('V99XXX'));
    }

    /**
     * @test
     */
    public function getProcedureReturnReturnsCorrectProcedureNumberForDomesticProducts()
    {
        static::assertEquals('07', \Dhl\Versenden\ParcelDe\Product::getProcedureReturn('V01PAK'));
        static::assertEquals('07', \Dhl\Versenden\ParcelDe\Product::getProcedureReturn('V62KP'));
    }

    /**
     * @test
     */
    public function getProcedureReturnReturnsEmptyStringForNonDomesticProducts()
    {
        static::assertEquals('', \Dhl\Versenden\ParcelDe\Product::getProcedureReturn('V53WPAK'));
        static::assertEquals('', \Dhl\Versenden\ParcelDe\Product::getProcedureReturn('V54EPAK'));
        static::assertEquals('', \Dhl\Versenden\ParcelDe\Product::getProcedureReturn('V66WPI'));
        static::assertEquals('', \Dhl\Versenden\ParcelDe\Product::getProcedureReturn('V06TG'));
        static::assertEquals('', \Dhl\Versenden\ParcelDe\Product::getProcedureReturn('V06WZ'));
    }

    /**
     * @test
     */
    public function getProcedureReturnReturnsEmptyStringForInvalidCode()
    {
        static::assertEquals('', \Dhl\Versenden\ParcelDe\Product::getProcedureReturn('INVALID'));
        static::assertEquals('', \Dhl\Versenden\ParcelDe\Product::getProcedureReturn(''));
    }

    /**
     * @test
     */
    public function getCodesByCountryReturnsDomesticProductsForDeToDeShipments()
    {
        $euCountries = ['AT', 'BE', 'FR', 'NL', 'PL'];
        $codes = \Dhl\Versenden\ParcelDe\Product::getCodesByCountry('DE', 'DE', $euCountries);

        static::assertCount(2, $codes);
        static::assertContains('V01PAK', $codes);
        static::assertContains('V62KP', $codes);
    }

    /**
     * @test
     */
    public function getCodesByCountryReturnsEuProductsForDeToEuShipments()
    {
        $euCountries = ['AT', 'BE', 'FR', 'NL', 'PL'];
        $codesAt = \Dhl\Versenden\ParcelDe\Product::getCodesByCountry('DE', 'AT', $euCountries);
        $codesFr = \Dhl\Versenden\ParcelDe\Product::getCodesByCountry('DE', 'FR', $euCountries);

        static::assertCount(2, $codesAt);
        static::assertContains('V53WPAK', $codesAt);
        static::assertContains('V66WPI', $codesAt);

        static::assertCount(2, $codesFr);
        static::assertContains('V53WPAK', $codesFr);
        static::assertContains('V66WPI', $codesFr);
    }

    /**
     * @test
     */
    public function getCodesByCountryReturnsRowProductsForDeToNonEuShipments()
    {
        $euCountries = ['AT', 'BE', 'FR', 'NL', 'PL'];
        $codesUs = \Dhl\Versenden\ParcelDe\Product::getCodesByCountry('DE', 'US', $euCountries);
        $codesCn = \Dhl\Versenden\ParcelDe\Product::getCodesByCountry('DE', 'CN', $euCountries);

        static::assertCount(2, $codesUs);
        static::assertContains('V53WPAK', $codesUs);
        static::assertContains('V66WPI', $codesUs);

        static::assertCount(2, $codesCn);
        static::assertContains('V53WPAK', $codesCn);
        static::assertContains('V66WPI', $codesCn);
    }

    /**
     * @test
     */
    public function getCodesByCountryReturnsEmptyArrayForNonDeShipperCountry()
    {
        $euCountries = ['AT', 'BE', 'FR', 'NL', 'PL'];
        $codes = \Dhl\Versenden\ParcelDe\Product::getCodesByCountry('US', 'DE', $euCountries);

        static::assertEmpty($codes);
        static::assertIsArray($codes);
    }
}
