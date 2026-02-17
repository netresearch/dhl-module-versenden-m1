<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Test_Helper_AddressTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @param string $street
     *
     * @test
     * @loadExpectation
     * @dataProvider dataProvider
     */
    public function splitStreet($street)
    {
        /** @var Dhl_Versenden_Helper_Address $helper */
        $helper   = Mage::helper('dhl_versenden/address');
        $split    = $helper->splitStreet($street);
        $expected = $this->expected('auto')->getData();

        static::assertEquals($expected, $split);
    }
}
