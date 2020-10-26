<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Test_Model_Adminhtml_System_Config_Source_YesoptnoTest
    extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     */
    public function toOptionArray()
    {
        $expected = array('Disable', 'Enable', 'Enable on customers choice');

        $source = new Dhl_Versenden_Model_Adminhtml_System_Config_Source_Yesoptno();
        $options = $source->toOptionArray();

        $this->assertInternalType('array', $options);
        $this->assertCount(3, $options);

        foreach ($options as $option) {
            $this->assertInternalType('array', $option);
            $this->assertArrayHasKey('value', $option);
            $this->assertArrayHasKey('label', $option);

            $this->assertArrayHasKey($option['value'], $expected);
            $this->assertEquals($expected[$option['value']], $option['label']);
        }
    }
}
