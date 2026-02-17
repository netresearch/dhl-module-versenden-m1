<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Test_Controller_Adminhtml_System_ConfigControllerTest extends Dhl_Versenden_Test_Case_AdminController
{
    /**
     * Dispatch admin route, assert blocks being loaded.
     * @see Dhl_Versenden_Block_Adminhtml_System_Config_Heading
     * @see Dhl_Versenden_Block_Adminhtml_System_Config_Info
     *
     * @test
     * @loadFixture Controller_ConfigTest
     */
    public function renderSection()
    {
        $this->dispatch('adminhtml/system_config/edit/section/carriers');

        $this->assertResponseBodyContains('DHL Versenden');
        $this->assertResponseBodyRegExp('/Version: \d\.\d{1,2}\.\d{1,2}/');
    }
}
