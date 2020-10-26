<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Test_Config_ModuleTest extends EcomDev_PHPUnit_Test_Case_Config
{
    /**
     * @test
     */
    public function validateCodePool()
    {
        $this->assertModuleCodePool('community');
    }
}
