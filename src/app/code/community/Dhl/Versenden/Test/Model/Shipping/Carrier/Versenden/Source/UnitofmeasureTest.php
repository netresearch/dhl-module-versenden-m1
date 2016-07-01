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

/**
 * Dhl_Versenden_Test_Model_Shipping_Carrier_Versenden_Source_UnitofmeasureTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Test_Model_Shipping_Carrier_Versenden_Source_UnitofmeasureTest
    extends EcomDev_PHPUnit_Test_Case
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
            ->expects($this->once())
            ->method('getCode')
            ->with('unit_of_measure')
            ->willReturn($units)
        ;
        $this->replaceByMock('singleton', 'dhl_versenden/shipping_carrier_versenden', $carrierMock);

        $sourceModel = new Dhl_Versenden_Model_Shipping_Carrier_Versenden_Source_Unitofmeasure();

        $options = $sourceModel->toOptionArray();
        $this->assertCount(count($units), $options);

        array_walk($options, function ($option) use ($units) {
            $this->assertInternalType('array', $option);
            $this->assertArrayHasKey('value', $option);
            $this->assertArrayHasKey('label', $option);
            $this->assertEquals($units[$option['value']], $option['label']);
        });
    }
}
