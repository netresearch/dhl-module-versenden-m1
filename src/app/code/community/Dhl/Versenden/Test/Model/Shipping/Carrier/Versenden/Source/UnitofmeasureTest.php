<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Test_Model_Shipping_Carrier_Versenden_Source_UnitofmeasureTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     */
    public function toOptionArray()
    {
        $units = [
            'G'  => 'foo',
            'KG' => 'bar',
        ];

        $carrierMock = $this->getModelMock('dhl_versenden/shipping_carrier_versenden', ['getCode']);
        $carrierMock
            ->expects(static::once())
            ->method('getCode')
            ->with('unit_of_measure')
            ->willReturn($units)
        ;
        $this->replaceByMock('singleton', 'dhl_versenden/shipping_carrier_versenden', $carrierMock);

        $sourceModel = new Dhl_Versenden_Model_Shipping_Carrier_Versenden_Source_Unitofmeasure();

        $options = $sourceModel->toOptionArray();
        static::assertCount(count($units), $options);

        array_walk($options, function ($option) use ($units) {
            $this->assertIsArray($option);
            $this->assertArrayHasKey('value', $option);
            $this->assertArrayHasKey('label', $option);
            $this->assertEquals($units[$option['value']], $option['label']);
        });
    }
}
