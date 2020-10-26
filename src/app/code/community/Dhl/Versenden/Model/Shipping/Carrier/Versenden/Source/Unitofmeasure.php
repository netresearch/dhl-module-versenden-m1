<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Model_Shipping_Carrier_Versenden_Source_Unitofmeasure
{
    public function toOptionArray()
    {
        $unitArr = Mage::getSingleton('dhl_versenden/shipping_carrier_versenden')
            ->getCode('unit_of_measure');

        $returnArr = array();
        foreach ($unitArr as $key => $val) {
            $returnArr[] = array(
                'value' => $key,
                'label' => $val,
            );
        }

        return $returnArr;
    }
}
