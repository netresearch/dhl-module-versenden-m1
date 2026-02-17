<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Test_Model_Webservice_Builder_PackageTest extends EcomDev_PHPUnit_Test_Case
{
    protected $minWeightInKG = 0.01;

    /**
     * @test
     */
    public function constructorArgUnitOfMeasureMissing()
    {
        $this->expectException(Mage_Core_Exception::class);

        $args = [
            'min_weight' => $this->minWeightInKG,
        ];
        Mage::getModel('dhl_versenden/webservice_builder_package', $args);
    }

    /**
     * @test
     */
    public function constructorArgUnitOfMeasureWrongType()
    {
        $this->expectException(Mage_Core_Exception::class);

        $args = [
            'unit_of_measure' => new stdClass(),
            'min_weight'      => $this->minWeightInKG,
        ];
        Mage::getModel('dhl_versenden/webservice_builder_package', $args);
    }

    /**
     * @test
     */
    public function constructorArgMinWeightMissing()
    {
        $this->expectException(Mage_Core_Exception::class);

        $args = [
            'unit_of_measure' => 'G',
        ];
        Mage::getModel('dhl_versenden/webservice_builder_package', $args);
    }

    /**
     * @test
     */
    public function constructorArgMinWeightWrongType()
    {
        $this->expectException(Mage_Core_Exception::class);

        $args = [
            'unit_of_measure' => 'G',
            'min_weight'      => new stdClass(),
        ];
        Mage::getModel('dhl_versenden/webservice_builder_package', $args);
    }


    /**
     * @test
     */
    public function buildPopulatesSdkBuilderWithPackageDetails()
    {
        // Create mock SDK builder
        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\Api\ShipmentOrderRequestBuilderInterface::class)
            ->getMock();

        $weightInKG = 1.5;
        $lengthInCM = 30;
        $widthInCM = 20;
        $heightInCM = 10;

        $packageInfo = [
            '1' => [
                'params' => [
                    'weight' => $weightInKG,
                    'length' => $lengthInCM,
                    'width' => $widthInCM,
                    'height' => $heightInCM,
                ],
            ],
        ];

        // Expect SDK builder methods to be called with correct values
        $sdkBuilder->expects(static::once())
            ->method('setPackageDetails')
            ->with($weightInKG);

        $sdkBuilder->expects(static::once())
            ->method('setPackageDimensions')
            ->with($widthInCM, $lengthInCM, $heightInCM);

        $args = [
            'unit_of_measure' => 'KG',
            'min_weight' => 0.01,
        ];
        $builder = Mage::getModel('dhl_versenden/webservice_builder_package', $args);

        // Test the build method - should populate SDK builder
        $result = $builder->build($sdkBuilder, $packageInfo);

        // Verify void return
        static::assertNull($result);
    }

    /**
     * @test
     */
    public function buildWithWeightConversionFromGrams()
    {
        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\Api\ShipmentOrderRequestBuilderInterface::class)
            ->getMock();

        $weightInG = 450.0;
        $expectedWeightInKG = 0.450;

        $packageInfo = [
            '1' => [
                'params' => [
                    'weight' => $weightInG,
                    'weight_units' => 'G',
                ],
            ],
        ];

        // Expect weight to be converted to KG
        $sdkBuilder->expects(static::once())
            ->method('setPackageDetails')
            ->with($expectedWeightInKG);

        $sdkBuilder->expects(static::never())
            ->method('setPackageDimensions');

        $args = [
            'unit_of_measure' => 'KG',
            'min_weight' => 0.01,
        ];
        $builder = Mage::getModel('dhl_versenden/webservice_builder_package', $args);

        $result = $builder->build($sdkBuilder, $packageInfo);
        static::assertNull($result);
    }

    /**
     * @test
     */
    public function buildEnforcesMinimumWeight()
    {
        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\Api\ShipmentOrderRequestBuilderInterface::class)
            ->getMock();

        $minWeightInKG = 0.01;
        $tooLightWeightInKG = 0.005;

        $packageInfo = [
            '1' => [
                'params' => [
                    'weight' => $tooLightWeightInKG,
                ],
            ],
        ];

        // Expect minimum weight to be enforced
        $sdkBuilder->expects(static::once())
            ->method('setPackageDetails')
            ->with($minWeightInKG);

        $args = [
            'unit_of_measure' => 'KG',
            'min_weight' => $minWeightInKG,
        ];
        $builder = Mage::getModel('dhl_versenden/webservice_builder_package', $args);

        $result = $builder->build($sdkBuilder, $packageInfo);
        static::assertNull($result);
    }

    /**
     * @test
     */
    public function buildWithWeightConversionFromPounds()
    {
        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\Api\ShipmentOrderRequestBuilderInterface::class)
            ->getMock();

        $weightInLBS = 5.0;
        $expectedWeightInKG = 2.26796; // 5 * 0.453592

        $packageInfo = [
            '1' => [
                'params' => [
                    'weight' => $weightInLBS,
                    'weight_units' => 'LBS',
                ],
            ],
        ];

        $sdkBuilder->expects(static::once())
            ->method('setPackageDetails')
            ->with(static::equalTo($expectedWeightInKG, 0.0001));

        $args = [
            'unit_of_measure' => 'KG',
            'min_weight' => 0.01,
        ];
        $builder = Mage::getModel('dhl_versenden/webservice_builder_package', $args);

        $builder->build($sdkBuilder, $packageInfo);
    }

    /**
     * @test
     */
    public function buildWithWeightConversionFromOunces()
    {
        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\Api\ShipmentOrderRequestBuilderInterface::class)
            ->getMock();

        $weightInOZ = 16.0; // 1 pound = 16 ounces
        $expectedWeightInKG = 0.453592; // 16 * 0.0283495

        $packageInfo = [
            '1' => [
                'params' => [
                    'weight' => $weightInOZ,
                    'weight_units' => 'OZ',
                ],
            ],
        ];

        $sdkBuilder->expects(static::once())
            ->method('setPackageDetails')
            ->with(static::equalTo($expectedWeightInKG, 0.0001));

        $args = [
            'unit_of_measure' => 'KG',
            'min_weight' => 0.01,
        ];
        $builder = Mage::getModel('dhl_versenden/webservice_builder_package', $args);

        $builder->build($sdkBuilder, $packageInfo);
    }
}
