<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Test_Model_Webservice_RequestData_VersionTest
    extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     */
    public function createVersion()
    {
        $major = '303';
        $minor = '808';
        $build = '909';

        $version = new \Dhl\Versenden\Bcs\Api\Webservice\RequestData\Version($major, $minor, $build);
        $this->assertEquals($major, $version->getMajorRelease());
        $this->assertEquals($minor, $version->getMinorRelease());
        $this->assertEquals($build, $version->getBuild());
    }

    /**
     * @test
     */
    public function createVersionSkipBuild()
    {
        $major = '303';
        $minor = '808';

        $version = new \Dhl\Versenden\Bcs\Api\Webservice\RequestData\Version($major, $minor);
        $this->assertEquals($major, $version->getMajorRelease());
        $this->assertEquals($minor, $version->getMinorRelease());
        $this->assertNull($version->getBuild());
    }
}
