<?php

/**
 * See LICENSE.md for license details.
 */

use \Dhl\Versenden\Bcs\Api\Webservice\ResponseData;

class Dhl_Versenden_Test_Model_Webservice_ResponseData_VersionTest
    extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     */
    public function version()
    {
        $versionNumber = '2.1';
        $version = new ResponseData\Version($versionNumber);
        $this->assertSame($versionNumber, $version->getVersion());
    }
}
