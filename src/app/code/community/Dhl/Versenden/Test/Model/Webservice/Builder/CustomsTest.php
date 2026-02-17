<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Test_Model_Webservice_Builder_CustomsTest extends EcomDev_PHPUnit_Test_Case
{
    protected $minWeightInKG = 0.01;

    /**
     * @return string[]
     */
    public function getWeightDataProvider()
    {
        return [
            'weight_in_kg' => [
                'weightUnit' => 'KG',
                'totalWeightInUnit' => 2.0000,
                'totalWeightInKg' => 2.0000,
                'itemOneWeightInUnit' => 0.4000,
                'itemOneWeightInKg' => 0.4000,
                'itemTwoWeightInUnit' => 1.2000,
                'itemTwoWeightInKg' => 1.2000,
            ],
            'weight_in_g' => [
                'weightUnit' => 'G',
                'totalWeightInUnit' => 2000.0000,
                'totalWeightInKg' => 2.0000,
                'itemOneWeightInUnit' => 400.0000,
                'itemOneWeightInKg' => 0.4000,
                'itemTwoWeightInUnit' => 1200.0000,
                'itemTwoWeightInKg' => 1.2000,
            ],
        ];
    }

    /**
     * @test
     */
    public function constructorArgUnitOfMeasureMissing()
    {
        $this->expectException(Mage_Core_Exception::class);

        $args = [
            'min_weight' => $this->minWeightInKG,
        ];
        Mage::getModel('dhl_versenden/webservice_builder_customs', $args);
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
        Mage::getModel('dhl_versenden/webservice_builder_customs', $args);
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
        Mage::getModel('dhl_versenden/webservice_builder_customs', $args);
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
        Mage::getModel('dhl_versenden/webservice_builder_customs', $args);
    }

    /**
     * Assert that customs builder does nothing when no customs info available.
     *
     * @test
     */
    public function noCustomsInfoAvailable()
    {
        $args = [
            'unit_of_measure' => 'KG',
            'min_weight' => $this->minWeightInKG,
        ];
        $invoiceNumber = '103000002';

        $customsInfo = [];
        $packageInfo = [];

        /** @var \Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder|PHPUnit_Framework_MockObject_MockObject $sdkBuilder */
        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->setMethods(['setCustomsDetails', 'addExportItem'])
            ->getMock();

        // No customs methods should be called when no customs info available
        $sdkBuilder->expects(static::never())->method('setCustomsDetails');
        $sdkBuilder->expects(static::never())->method('addExportItem');

        $builder = Mage::getModel('dhl_versenden/webservice_builder_customs', $args);
        $builder->build($sdkBuilder, $invoiceNumber, $customsInfo, $packageInfo);
    }

    /**
     * @test
     * @dataProvider getWeightDataProvider
     *
     * @param string $weightUnit
     * @param float $totalWeight
     * @param float $itemOneWeight
     * @param float $itemTwoWeight
     */
    public function build(
        $weightUnit,
        $totalWeightInUnit,
        $totalWeightInKg,
        $itemOneWeightInUnit,
        $itemOneWeightInKg,
        $itemTwoWeightInUnit,
        $itemTwoWeightInKg
    ) {
        $args = ['unit_of_measure' => $weightUnit, 'min_weight' => $this->minWeightInKG];

        // prepare data
        $invoiceNumber = '103000002';

        $packageSequenceNumber = '303';
        $itemSequenceNumberOne = '808';
        $itemSequenceNumberTwo = '909';

        $packageItemOne = [
            'qty' => 2,
            'weight' => $itemOneWeightInUnit,
            'customs_value' => 9.95,
            'customs' => [
                'description' => 'one',
                'country_of_origin' => 'TR',
                'tariff_number' => '101010',
            ],
        ];
        $packageItemTwo = [
            'qty' => 1,
            'weight' => $itemTwoWeightInUnit,
            'customs_value' => 19.95,
            'customs' => [
                'description' => 'two',
                'country_of_origin' => 'DE',
                'tariff_number' => '202020',
            ],
        ];

        $package = [
            'params' => [
                'weight' => $totalWeightInUnit,
                'content_type' => Dhl_Versenden_Model_Shipping_Carrier_Versenden::EXPORT_TYPE_OTHER,
                'content_type_other' => 'Foo',
            ],
            'items' => [
                $itemSequenceNumberOne => $packageItemOne,
                $itemSequenceNumberTwo => $packageItemTwo,
            ],
        ];

        $customsInfo = [
            'terms_of_trade' => Dhl_Versenden_Model_Shipping_Carrier_Versenden::TOT_DDX,
            'additional_fee' => 2,
            'place_of_commital' => 'LE',
            'permit_number' => '123',
            'attestation_number' => '456',
            'export_notification' => true,
        ];
        $packageInfo = [$packageSequenceNumber => $package];

        /** @var \Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder|PHPUnit_Framework_MockObject_MockObject $sdkBuilder */
        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->setMethods(['setCustomsDetails', 'addExportItem'])
            ->getMock();

        // Expect setCustomsDetails to be called once per package
        $sdkBuilder->expects(static::once())
            ->method('setCustomsDetails')
            ->with(
                $package['params']['content_type'],
                $customsInfo['place_of_commital'],
                $customsInfo['additional_fee'],
                $package['params']['content_type_other'],
                $customsInfo['terms_of_trade'],
                $invoiceNumber,
                $customsInfo['permit_number'],
                $customsInfo['attestation_number'],
                $customsInfo['export_notification'],
                null,
                null,
                null,
            );

        // Expect addExportItem to be called once per item (2 items)
        // Note: country_of_origin is converted from ISO-2 (form input) to ISO-3 (API requirement)
        $sdkBuilder->expects(static::exactly(2))
            ->method('addExportItem')
            ->withConsecutive(
                [
                    $packageItemOne['qty'],
                    $packageItemOne['customs']['description'],
                    $packageItemOne['customs_value'],
                    $itemOneWeightInKg,
                    $packageItemOne['customs']['tariff_number'],
                    'TUR', // ISO-3 converted from 'TR'
                ],
                [
                    $packageItemTwo['qty'],
                    $packageItemTwo['customs']['description'],
                    $packageItemTwo['customs_value'],
                    $itemTwoWeightInKg,
                    $packageItemTwo['customs']['tariff_number'],
                    'DEU', // ISO-3 converted from 'DE'
                ],
            );

        $builder = Mage::getModel('dhl_versenden/webservice_builder_customs', $args);
        $builder->build($sdkBuilder, $invoiceNumber, $customsInfo, $packageInfo);
    }

    /**
     * @test
     */
    public function buildThrowsOnMissingPackageParams()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Package params missing');

        $args = ['unit_of_measure' => 'KG', 'min_weight' => $this->minWeightInKG];

        /** @var \Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder|PHPUnit_Framework_MockObject_MockObject $sdkBuilder */
        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Package without 'params' key
        $customsInfo = ['terms_of_trade' => 'DDP'];
        $packageInfo = ['1' => ['items' => []]]; // Missing 'params'

        $builder = Mage::getModel('dhl_versenden/webservice_builder_customs', $args);
        $builder->build($sdkBuilder, '123', $customsInfo, $packageInfo);
    }

    /**
     * @test
     */
    public function buildThrowsOnMissingContentType()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Content type required');

        $args = ['unit_of_measure' => 'KG', 'min_weight' => $this->minWeightInKG];

        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Package with params but no content_type
        $customsInfo = ['terms_of_trade' => 'DDP'];
        $packageInfo = ['1' => ['params' => ['weight' => 1.0]]]; // Missing 'content_type'

        $builder = Mage::getModel('dhl_versenden/webservice_builder_customs', $args);
        $builder->build($sdkBuilder, '123', $customsInfo, $packageInfo);
    }

    /**
     * @test
     */
    public function buildThrowsOnMissingItems()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Items required');

        $args = ['unit_of_measure' => 'KG', 'min_weight' => $this->minWeightInKG];

        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['setCustomsDetails'])
            ->getMock();

        // Package with content_type but no items
        $customsInfo = ['terms_of_trade' => 'DDP'];
        $packageInfo = ['1' => ['params' => ['content_type' => 'OTHER']]]; // Missing 'items'

        $builder = Mage::getModel('dhl_versenden/webservice_builder_customs', $args);
        $builder->build($sdkBuilder, '123', $customsInfo, $packageInfo);
    }

    /**
     * @test
     */
    public function buildThrowsOnMissingItemQty()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Item quantity missing');

        $args = ['unit_of_measure' => 'KG', 'min_weight' => $this->minWeightInKG];

        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['setCustomsDetails'])
            ->getMock();

        $customsInfo = ['terms_of_trade' => 'DDP'];
        $packageInfo = ['1' => [
            'params' => ['content_type' => 'OTHER'],
            'items' => ['item1' => ['weight' => 1.0]], // Missing 'qty'
        ]];

        $builder = Mage::getModel('dhl_versenden/webservice_builder_customs', $args);
        $builder->build($sdkBuilder, '123', $customsInfo, $packageInfo);
    }

    /**
     * @test
     */
    public function buildThrowsOnMissingItemWeight()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Item weight missing');

        $args = ['unit_of_measure' => 'KG', 'min_weight' => $this->minWeightInKG];

        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['setCustomsDetails'])
            ->getMock();

        $customsInfo = ['terms_of_trade' => 'DDP'];
        $packageInfo = ['1' => [
            'params' => ['content_type' => 'OTHER'],
            'items' => ['item1' => ['qty' => 1]], // Missing 'weight'
        ]];

        $builder = Mage::getModel('dhl_versenden/webservice_builder_customs', $args);
        $builder->build($sdkBuilder, '123', $customsInfo, $packageInfo);
    }

    /**
     * @test
     */
    public function buildThrowsOnMissingCustomsValue()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Item customs value missing');

        $args = ['unit_of_measure' => 'KG', 'min_weight' => $this->minWeightInKG];

        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['setCustomsDetails'])
            ->getMock();

        $customsInfo = ['terms_of_trade' => 'DDP'];
        $packageInfo = ['1' => [
            'params' => ['content_type' => 'OTHER'],
            'items' => ['item1' => ['qty' => 1, 'weight' => 1.0]], // Missing 'customs_value'
        ]];

        $builder = Mage::getModel('dhl_versenden/webservice_builder_customs', $args);
        $builder->build($sdkBuilder, '123', $customsInfo, $packageInfo);
    }

    /**
     * @test
     */
    public function buildThrowsOnMissingCustomsData()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Item customs data missing');

        $args = ['unit_of_measure' => 'KG', 'min_weight' => $this->minWeightInKG];

        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['setCustomsDetails'])
            ->getMock();

        $customsInfo = ['terms_of_trade' => 'DDP'];
        $packageInfo = ['1' => [
            'params' => ['content_type' => 'OTHER'],
            'items' => ['item1' => ['qty' => 1, 'weight' => 1.0, 'customs_value' => 10.0]], // Missing 'customs'
        ]];

        $builder = Mage::getModel('dhl_versenden/webservice_builder_customs', $args);
        $builder->build($sdkBuilder, '123', $customsInfo, $packageInfo);
    }

    /**
     * @test
     */
    public function buildThrowsOnMissingDescription()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Item description required');

        $args = ['unit_of_measure' => 'KG', 'min_weight' => $this->minWeightInKG];

        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['setCustomsDetails'])
            ->getMock();

        $customsInfo = ['terms_of_trade' => 'DDP'];
        $packageInfo = ['1' => [
            'params' => ['content_type' => 'OTHER'],
            'items' => ['item1' => [
                'qty' => 1,
                'weight' => 1.0,
                'customs_value' => 10.0,
                'customs' => ['tariff_number' => '123456', 'country_of_origin' => 'DE'], // Missing 'description'
            ]],
        ]];

        $builder = Mage::getModel('dhl_versenden/webservice_builder_customs', $args);
        $builder->build($sdkBuilder, '123', $customsInfo, $packageInfo);
    }

    /**
     * @test
     */
    public function buildThrowsOnMissingTariffNumber()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Item tariff number required');

        $args = ['unit_of_measure' => 'KG', 'min_weight' => $this->minWeightInKG];

        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['setCustomsDetails'])
            ->getMock();

        $customsInfo = ['terms_of_trade' => 'DDP'];
        $packageInfo = ['1' => [
            'params' => ['content_type' => 'OTHER'],
            'items' => ['item1' => [
                'qty' => 1,
                'weight' => 1.0,
                'customs_value' => 10.0,
                'customs' => ['description' => 'Test', 'country_of_origin' => 'DE'], // Missing 'tariff_number'
            ]],
        ]];

        $builder = Mage::getModel('dhl_versenden/webservice_builder_customs', $args);
        $builder->build($sdkBuilder, '123', $customsInfo, $packageInfo);
    }

    /**
     * @test
     */
    public function buildThrowsOnMissingCountryOfOrigin()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Item country of origin required');

        $args = ['unit_of_measure' => 'KG', 'min_weight' => $this->minWeightInKG];

        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['setCustomsDetails'])
            ->getMock();

        $customsInfo = ['terms_of_trade' => 'DDP'];
        $packageInfo = ['1' => [
            'params' => ['content_type' => 'OTHER'],
            'items' => ['item1' => [
                'qty' => 1,
                'weight' => 1.0,
                'customs_value' => 10.0,
                'customs' => ['description' => 'Test', 'tariff_number' => '123456'], // Missing 'country_of_origin'
            ]],
        ]];

        $builder = Mage::getModel('dhl_versenden/webservice_builder_customs', $args);
        $builder->build($sdkBuilder, '123', $customsInfo, $packageInfo);
    }

    /**
     * Assert that MRN (Master Reference Number) is passed to SDK when provided.
     *
     * @test
     */
    public function buildPassesMasterReferenceNumber()
    {
        $args = ['unit_of_measure' => 'KG', 'min_weight' => $this->minWeightInKG];
        $mrn = 'MRN123456789';

        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['setCustomsDetails', 'addExportItem'])
            ->getMock();

        $customsInfo = [
            'terms_of_trade' => 'DDP',
            'master_reference_number' => $mrn,
        ];
        $packageInfo = ['1' => [
            'params' => ['content_type' => 'OTHER'],
            'items' => ['item1' => [
                'qty' => 1,
                'weight' => 1.0,
                'customs_value' => 10.0,
                'customs' => ['description' => 'Test', 'tariff_number' => '123456', 'country_of_origin' => 'DE'],
            ]],
        ]];

        // Expect MRN to be passed as 12th parameter to setCustomsDetails
        $sdkBuilder->expects(static::once())
            ->method('setCustomsDetails')
            ->with(
                'OTHER',    // content_type
                '',         // place_of_commital
                0.0,        // additional_fee
                '',         // content_type_other
                'DDP',      // terms_of_trade
                '123',      // invoiceNumber
                '',         // permit_number
                '',         // attestation_number
                false,      // export_notification
                null,       // sendersCustomsReference
                null,       // addresseesCustomsReference
                $mrn        // masterReferenceNumber (MRN)
            );

        $builder = Mage::getModel('dhl_versenden/webservice_builder_customs', $args);
        $builder->build($sdkBuilder, '123', $customsInfo, $packageInfo);
    }

    /**
     * Assert that minimum weight is enforced.
     *
     * @test
     */
    public function buildEnforcesMinWeight()
    {
        $minWeight = 0.1; // 100 grams minimum
        $args = ['unit_of_measure' => 'KG', 'min_weight' => $minWeight];

        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['setCustomsDetails', 'addExportItem'])
            ->getMock();

        // Item with weight below minimum
        $customsInfo = ['terms_of_trade' => 'DDP'];
        $packageInfo = ['1' => [
            'params' => ['content_type' => 'OTHER'],
            'items' => ['item1' => [
                'qty' => 1,
                'weight' => 0.01, // Below minimum
                'customs_value' => 10.0,
                'customs' => ['description' => 'Test', 'tariff_number' => '123456', 'country_of_origin' => 'DE'],
            ]],
        ]];

        // Expect weight to be capped at minimum (0.1 KG)
        // Note: country_of_origin 'DE' is converted to ISO-3 'DEU'
        $sdkBuilder->expects(static::once())
            ->method('addExportItem')
            ->with(1, 'Test', 10.0, $minWeight, '123456', 'DEU');

        $builder = Mage::getModel('dhl_versenden/webservice_builder_customs', $args);
        $builder->build($sdkBuilder, '123', $customsInfo, $packageInfo);
    }

    /**
     * Assert that HS code (tariff number) with 6 digits is accepted.
     *
     * @test
     */
    public function hsCodeAccepts6Digits()
    {
        $args = ['unit_of_measure' => 'KG', 'min_weight' => $this->minWeightInKG];

        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['setCustomsDetails', 'addExportItem'])
            ->getMock();

        $customsInfo = ['terms_of_trade' => 'DDP'];
        $packageInfo = ['1' => [
            'params' => ['content_type' => 'OTHER'],
            'items' => ['item1' => [
                'qty' => 1,
                'weight' => 1.0,
                'customs_value' => 10.0,
                'customs' => ['description' => 'Test', 'tariff_number' => '123456', 'country_of_origin' => 'DE'],
            ]],
        ]];

        // Should succeed without exception
        // Note: country_of_origin 'DE' is converted to ISO-3 'DEU'
        $sdkBuilder->expects(static::once())
            ->method('addExportItem')
            ->with(1, 'Test', 10.0, 1.0, '123456', 'DEU');

        $builder = Mage::getModel('dhl_versenden/webservice_builder_customs', $args);
        $builder->build($sdkBuilder, '123', $customsInfo, $packageInfo);
    }

    /**
     * Assert that HS code (tariff number) with 11 digits is accepted.
     *
     * @test
     */
    public function hsCodeAccepts11Digits()
    {
        $args = ['unit_of_measure' => 'KG', 'min_weight' => $this->minWeightInKG];

        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['setCustomsDetails', 'addExportItem'])
            ->getMock();

        $customsInfo = ['terms_of_trade' => 'DDP'];
        $packageInfo = ['1' => [
            'params' => ['content_type' => 'OTHER'],
            'items' => ['item1' => [
                'qty' => 1,
                'weight' => 1.0,
                'customs_value' => 10.0,
                'customs' => ['description' => 'Test', 'tariff_number' => '12345678901', 'country_of_origin' => 'DE'],
            ]],
        ]];

        // Should succeed without exception
        // Note: country_of_origin 'DE' is converted to ISO-3 'DEU'
        $sdkBuilder->expects(static::once())
            ->method('addExportItem')
            ->with(1, 'Test', 10.0, 1.0, '12345678901', 'DEU');

        $builder = Mage::getModel('dhl_versenden/webservice_builder_customs', $args);
        $builder->build($sdkBuilder, '123', $customsInfo, $packageInfo);
    }

    /**
     * Assert that HS code (tariff number) with 5 digits is rejected.
     *
     * @test
     */
    public function hsCodeRejects5Digits()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('HS code must be between 6 and 11 digits');

        $args = ['unit_of_measure' => 'KG', 'min_weight' => $this->minWeightInKG];

        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['setCustomsDetails'])
            ->getMock();

        $customsInfo = ['terms_of_trade' => 'DDP'];
        $packageInfo = ['1' => [
            'params' => ['content_type' => 'OTHER'],
            'items' => ['item1' => [
                'qty' => 1,
                'weight' => 1.0,
                'customs_value' => 10.0,
                'customs' => ['description' => 'Test', 'tariff_number' => '12345', 'country_of_origin' => 'DE'], // 5 digits - too short
            ]],
        ]];

        $builder = Mage::getModel('dhl_versenden/webservice_builder_customs', $args);
        $builder->build($sdkBuilder, '123', $customsInfo, $packageInfo);
    }

    /**
     * Assert that HS code (tariff number) with 12 digits is rejected.
     *
     * @test
     */
    public function hsCodeRejects12Digits()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('HS code must be between 6 and 11 digits');

        $args = ['unit_of_measure' => 'KG', 'min_weight' => $this->minWeightInKG];

        $sdkBuilder = $this->getMockBuilder(\Dhl\Sdk\ParcelDe\Shipping\RequestBuilder\ShipmentOrderRequestBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['setCustomsDetails'])
            ->getMock();

        $customsInfo = ['terms_of_trade' => 'DDP'];
        $packageInfo = ['1' => [
            'params' => ['content_type' => 'OTHER'],
            'items' => ['item1' => [
                'qty' => 1,
                'weight' => 1.0,
                'customs_value' => 10.0,
                'customs' => ['description' => 'Test', 'tariff_number' => '123456789012', 'country_of_origin' => 'DE'], // 12 digits - too long
            ]],
        ]];

        $builder = Mage::getModel('dhl_versenden/webservice_builder_customs', $args);
        $builder->build($sdkBuilder, '123', $customsInfo, $packageInfo);
    }
}
