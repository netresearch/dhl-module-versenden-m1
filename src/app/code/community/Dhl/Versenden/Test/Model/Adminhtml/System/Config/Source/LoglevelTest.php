<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Test_Model_Adminhtml_System_Config_Source_LoglevelTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     */
    public function sourceModel()
    {
        $sourceModel = Mage::getModel('dhl_versenden/adminhtml_system_config_source_loglevel');
        $optionArray = $sourceModel->toOptionArray();

        static::assertIsArray($optionArray);
        static::assertNotEmpty($optionArray);

        foreach ($optionArray as $option) {
            static::assertIsArray($option);
            static::assertArrayHasKey('value', $option);
            static::assertArrayHasKey('label', $option);
            static::assertIsInt($option['value']);
            static::assertLessThanOrEqual(Zend_Log::DEBUG, $option['value']);
        }
    }
}
