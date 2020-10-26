<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Test_Model_Adminhtml_System_Config_Source_LoglevelTest
    extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     */
    public function sourceModel()
    {
        $sourceModel = Mage::getModel('dhl_versenden/adminhtml_system_config_source_loglevel');
        $optionArray = $sourceModel->toOptionArray();

        $this->assertInternalType('array', $optionArray);
        $this->assertNotEmpty($optionArray);

        foreach ($optionArray as $option) {
            $this->assertInternalType('array', $option);
            $this->assertArrayHasKey('value', $option);
            $this->assertArrayHasKey('label', $option);
            $this->assertInternalType('int', $option['value']);
            $this->assertLessThanOrEqual(Zend_Log::DEBUG, $option['value']);
        }
    }
}
