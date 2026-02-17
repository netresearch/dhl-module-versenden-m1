<?php

/**
 * Unit tests for ProductServiceMatrix.
 *
 * @see Dhl\Versenden\ParcelDe\Service\ProductServiceMatrix
 */
class Dhl_Versenden_Test_Unit_Lib_ParcelDe_Service_ProductServiceMatrixTest extends EcomDev_PHPUnit_Test_Case
{
    /** @var \Dhl\Versenden\ParcelDe\Service\ProductServiceMatrix */
    private $matrix;

    protected function setUp(): void
    {
        parent::setUp();
        $this->matrix = new \Dhl\Versenden\ParcelDe\Service\ProductServiceMatrix();
    }

    /**
     * @test
     * @testdox Matrix contains entries for all 7 products
     */
    public function matrixContainsAllProducts()
    {
        $matrix = $this->matrix->getMatrix();
        static::assertNotEmpty($matrix);

        $expectedProducts = \Dhl\Versenden\ParcelDe\Product::getCodes();
        foreach ($expectedProducts as $product) {
            static::assertArrayHasKey($product, $matrix, "Product $product must have a matrix entry");
        }
    }

    /**
     * @test
     * @testdox V01PAK has full domestic service set
     */
    public function v01pakHasFullServiceSet()
    {
        $services = $this->matrix->getServicesForProduct(
            \Dhl\Versenden\ParcelDe\Product::CODE_PAKET_NATIONAL
        );

        static::assertContains(\Dhl\Versenden\ParcelDe\Service\Cod::CODE, $services);
        static::assertContains(\Dhl\Versenden\ParcelDe\Service\AdditionalInsurance::CODE, $services);
        static::assertContains(\Dhl\Versenden\ParcelDe\Service\BulkyGoods::CODE, $services);
        static::assertContains(\Dhl\Versenden\ParcelDe\Service\GoGreenPlus::CODE, $services);
        static::assertContains(\Dhl\Versenden\ParcelDe\Service\VisualCheckOfAge::CODE, $services);
        static::assertContains(\Dhl\Versenden\ParcelDe\Service\ReturnShipment::CODE, $services);
        static::assertContains(\Dhl\Versenden\ParcelDe\Service\ParcelOutletRouting::CODE, $services);
        static::assertContains(\Dhl\Versenden\ParcelDe\Service\NamedPersonOnly::CODE, $services);
        static::assertContains(\Dhl\Versenden\ParcelDe\Service\NoNeighbourDelivery::CODE, $services);
        // IdentCheck removed - service deprecated
    }

    /**
     * @test
     * @testdox V62KP has limited services only
     */
    public function v62kpHasLimitedServices()
    {
        $services = $this->matrix->getServicesForProduct(
            \Dhl\Versenden\ParcelDe\Product::CODE_KLEINPAKET
        );

        // Must have these
        static::assertContains(\Dhl\Versenden\ParcelDe\Service\ParcelAnnouncement::CODE, $services);

        // Must NOT have these
        static::assertNotContains(\Dhl\Versenden\ParcelDe\Service\AdditionalInsurance::CODE, $services);
        static::assertNotContains(\Dhl\Versenden\ParcelDe\Service\BulkyGoods::CODE, $services);
        static::assertNotContains(\Dhl\Versenden\ParcelDe\Service\Cod::CODE, $services);
    }

    /**
     * @test
     * @testdox V66WPI has limited services: DeliveryType, ParcelAnnouncement
     */
    public function v66wpiHasLimitedServices()
    {
        $services = $this->matrix->getServicesForProduct(
            \Dhl\Versenden\ParcelDe\Product::CODE_WARENPOST_INTERNATIONAL
        );

        static::assertContains(\Dhl\Versenden\ParcelDe\Service\DeliveryType::CODE, $services);
        static::assertContains(\Dhl\Versenden\ParcelDe\Service\ParcelAnnouncement::CODE, $services);

        static::assertNotContains(\Dhl\Versenden\ParcelDe\Service\AdditionalInsurance::CODE, $services);
        static::assertNotContains(\Dhl\Versenden\ParcelDe\Service\BulkyGoods::CODE, $services);
        static::assertNotContains(\Dhl\Versenden\ParcelDe\Service\Endorsement::CODE, $services);
    }

    /**
     * @test
     * @testdox Unknown product returns empty array
     */
    public function unknownProductReturnsEmptyArray()
    {
        $services = $this->matrix->getServicesForProduct('UNKNOWN');
        static::assertIsArray($services);
        static::assertEmpty($services);
    }

    /**
     * @test
     * @testdox isServiceAllowedForProduct correctly reports allowed/disallowed
     */
    public function isServiceAllowedForProduct()
    {
        static::assertTrue(
            $this->matrix->isServiceAllowedForProduct(
                \Dhl\Versenden\ParcelDe\Service\AdditionalInsurance::CODE,
                \Dhl\Versenden\ParcelDe\Product::CODE_PAKET_NATIONAL
            ),
            'AdditionalInsurance must be allowed for V01PAK'
        );

        static::assertFalse(
            $this->matrix->isServiceAllowedForProduct(
                \Dhl\Versenden\ParcelDe\Service\AdditionalInsurance::CODE,
                \Dhl\Versenden\ParcelDe\Product::CODE_KLEINPAKET
            ),
            'AdditionalInsurance must NOT be allowed for V62KP'
        );
    }

    /**
     * @test
     * @testdox V53WPAK has international-specific services
     */
    public function v53wpakHasInternationalServices()
    {
        $services = $this->matrix->getServicesForProduct(
            \Dhl\Versenden\ParcelDe\Product::CODE_WELTPAKET
        );

        static::assertContains(\Dhl\Versenden\ParcelDe\Service\Endorsement::CODE, $services);
        static::assertContains(\Dhl\Versenden\ParcelDe\Service\PostalDeliveryDutyPaid::CODE, $services);
        static::assertContains(\Dhl\Versenden\ParcelDe\Service\DeliveryType::CODE, $services);
        static::assertContains(\Dhl\Versenden\ParcelDe\Service\ClosestDropPoint::CODE, $services);

        // Must NOT have domestic-only services
        static::assertNotContains(\Dhl\Versenden\ParcelDe\Service\PreferredDay::CODE, $services);
    }

    /**
     * @test
     * @testdox V53WPAK includes COD for international shipments
     */
    public function v53wpakHasCod()
    {
        $services = $this->matrix->getServicesForProduct(
            \Dhl\Versenden\ParcelDe\Product::CODE_WELTPAKET
        );

        static::assertContains(\Dhl\Versenden\ParcelDe\Service\Cod::CODE, $services);
    }

    /**
     * @test
     * @testdox V53WPAK does not include ReturnShipment (domestic DE only)
     */
    public function v53wpakDoesNotHaveReturnShipment()
    {
        $services = $this->matrix->getServicesForProduct(
            \Dhl\Versenden\ParcelDe\Product::CODE_WELTPAKET
        );

        static::assertNotContains(\Dhl\Versenden\ParcelDe\Service\ReturnShipment::CODE, $services);
    }

    /**
     * @test
     * @testdox V62KP includes ReturnShipment and GoGreen
     */
    public function v62kpHasReturnShipmentAndGoGreen()
    {
        $services = $this->matrix->getServicesForProduct(
            \Dhl\Versenden\ParcelDe\Product::CODE_KLEINPAKET
        );

        static::assertContains(\Dhl\Versenden\ParcelDe\Service\ReturnShipment::CODE, $services);
        static::assertContains(\Dhl\Versenden\ParcelDe\Service\GoGreenPlus::CODE, $services);
    }

}
