<?php

/**
 * Unit tests for Service Filter.
 *
 * Tests the product-service matrix filtering logic, particularly
 * for V66WPI (Warenpost International) which was added in DHLGKP-337.
 *
 * @see Dhl\Versenden\ParcelDe\Service\Filter
 */
class Dhl_Versenden_Test_Unit_Lib_ParcelDe_Service_FilterTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     * @testdox V66WPI product is supported by Filter (ParcelAnnouncement service works)
     */
    public function v66wpiProductExistsInFilter()
    {
        $filter = new \Dhl\Versenden\ParcelDe\Service\Filter(
            [\Dhl\Versenden\ParcelDe\Product::CODE_WARENPOST_INTERNATIONAL],
            false,
            false
        );

        // Verify V66WPI is in productsServices by testing that an allowed service passes
        // If V66WPI wasn't in the array, filterService would return null for all services
        $service = new \Dhl\Versenden\ParcelDe\Service\ParcelAnnouncement(
            'parcelAnnouncement',
            true,
            false
        );

        $result = $filter->filterService($service);
        static::assertNotNull(
            $result,
            'V66WPI must be in productsServices array - ParcelAnnouncement should pass'
        );
    }

    /**
     * @test
     * @testdox V66WPI allows ParcelAnnouncement service
     */
    public function v66wpiAllowsParcelAnnouncement()
    {
        $filter = new \Dhl\Versenden\ParcelDe\Service\Filter(
            [\Dhl\Versenden\ParcelDe\Product::CODE_WARENPOST_INTERNATIONAL],
            false,
            false
        );

        $service = new \Dhl\Versenden\ParcelDe\Service\ParcelAnnouncement(
            'parcelAnnouncement',
            true,
            false
        );

        $result = $filter->filterService($service);
        static::assertNotNull($result, 'ParcelAnnouncement should be allowed for V66WPI');
        static::assertEquals(
            \Dhl\Versenden\ParcelDe\Service\ParcelAnnouncement::CODE,
            $result->getCode()
        );
    }

    /**
     * @test
     * @testdox V66WPI blocks AdditionalInsurance service
     */
    public function v66wpiBlocksAdditionalInsurance()
    {
        $filter = new \Dhl\Versenden\ParcelDe\Service\Filter(
            [\Dhl\Versenden\ParcelDe\Product::CODE_WARENPOST_INTERNATIONAL],
            false,
            false
        );

        $service = new \Dhl\Versenden\ParcelDe\Service\AdditionalInsurance(
            'additionalInsurance',
            true,
            false,
            ''
        );

        $result = $filter->filterService($service);
        static::assertNull($result, 'AdditionalInsurance should be blocked for V66WPI');
    }

    /**
     * @test
     * @testdox V66WPI blocks BulkyGoods service
     */
    public function v66wpiBlocksBulkyGoods()
    {
        $filter = new \Dhl\Versenden\ParcelDe\Service\Filter(
            [\Dhl\Versenden\ParcelDe\Product::CODE_WARENPOST_INTERNATIONAL],
            false,
            false
        );

        $service = new \Dhl\Versenden\ParcelDe\Service\BulkyGoods(
            'bulkyGoods',
            true,
            false
        );

        $result = $filter->filterService($service);
        static::assertNull($result, 'BulkyGoods should be blocked for V66WPI');
    }

    /**
     * @test
     * @testdox V66WPI blocks COD service
     */
    public function v66wpiBlocksCod()
    {
        $filter = new \Dhl\Versenden\ParcelDe\Service\Filter(
            [\Dhl\Versenden\ParcelDe\Product::CODE_WARENPOST_INTERNATIONAL],
            false,
            false
        );

        $service = new \Dhl\Versenden\ParcelDe\Service\Cod(
            'cod',
            true,
            false,
            ''
        );

        $result = $filter->filterService($service);
        static::assertNull($result, 'COD should be blocked for V66WPI');
    }

    /**
     * @test
     * @testdox V66WPI blocks ReturnShipment service
     */
    public function v66wpiBlocksReturnShipment()
    {
        $filter = new \Dhl\Versenden\ParcelDe\Service\Filter(
            [\Dhl\Versenden\ParcelDe\Product::CODE_WARENPOST_INTERNATIONAL],
            false,
            false
        );

        $service = new \Dhl\Versenden\ParcelDe\Service\ReturnShipment(
            'returnShipment',
            true,
            false
        );

        $result = $filter->filterService($service);
        static::assertNull($result, 'ReturnShipment should be blocked for V66WPI');
    }

    /**
     * @test
     * @testdox V66WPI blocks PreferredDay service
     */
    public function v66wpiBlocksPreferredDay()
    {
        $filter = new \Dhl\Versenden\ParcelDe\Service\Filter(
            [\Dhl\Versenden\ParcelDe\Product::CODE_WARENPOST_INTERNATIONAL],
            false,
            false
        );

        $service = new \Dhl\Versenden\ParcelDe\Service\PreferredDay(
            'preferredDay',
            true,
            false,
            [] // options array required
        );

        $result = $filter->filterService($service);
        static::assertNull($result, 'PreferredDay should be blocked for V66WPI');
    }

    /**
     * @test
     * @testdox V66WPI blocks PreferredLocation service
     */
    public function v66wpiBlocksPreferredLocation()
    {
        $filter = new \Dhl\Versenden\ParcelDe\Service\Filter(
            [\Dhl\Versenden\ParcelDe\Product::CODE_WARENPOST_INTERNATIONAL],
            false,
            false
        );

        $service = new \Dhl\Versenden\ParcelDe\Service\PreferredLocation(
            'preferredLocation',
            true,
            false,
            'Enter location' // placeholder required
        );

        $result = $filter->filterService($service);
        static::assertNull($result, 'PreferredLocation should be blocked for V66WPI');
    }

    /**
     * @test
     * @testdox Filter with multiple products allows service if any product supports it
     */
    public function filterWithMultipleProductsAllowsServiceFromAnyProduct()
    {
        // V53WPAK allows AdditionalInsurance, V66WPI does not
        $filter = new \Dhl\Versenden\ParcelDe\Service\Filter(
            [
                \Dhl\Versenden\ParcelDe\Product::CODE_WELTPAKET,
                \Dhl\Versenden\ParcelDe\Product::CODE_WARENPOST_INTERNATIONAL,
            ],
            false,
            false
        );

        $service = new \Dhl\Versenden\ParcelDe\Service\AdditionalInsurance(
            'additionalInsurance',
            true,
            false,
            ''
        );

        $result = $filter->filterService($service);
        static::assertNotNull(
            $result,
            'AdditionalInsurance should be allowed when V53WPAK is in product list (even if V66WPI is also present)'
        );
    }

    /**
     * @test
     * @testdox All products in Product::getCodes() are supported by Filter
     */
    public function allProductsHaveFilterEntries()
    {
        $productCodes = \Dhl\Versenden\ParcelDe\Product::getCodes();

        // Verify each product is in productsServices by testing that at least one service works
        // We use ParcelAnnouncement as it's a universally available basic service
        foreach ($productCodes as $productCode) {
            $filter = new \Dhl\Versenden\ParcelDe\Service\Filter(
                [$productCode],
                false,
                false
            );

            // ParcelAnnouncement is allowed for all products
            $service = new \Dhl\Versenden\ParcelDe\Service\ParcelAnnouncement(
                'parcelAnnouncement',
                true,
                false
            );

            $result = $filter->filterService($service);
            static::assertNotNull(
                $result,
                "Product $productCode must have an entry in productsServices - ParcelAnnouncement should work"
            );
        }
    }
}
