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
 * Dhl_Versenden_Test_Model_Webservice_RequestData_VersionTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
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

        $version = new \Dhl\Versenden\Webservice\RequestData\Version($major, $minor, $build);
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

        $version = new \Dhl\Versenden\Webservice\RequestData\Version($major, $minor);
        $this->assertEquals($major, $version->getMajorRelease());
        $this->assertEquals($minor, $version->getMinorRelease());
        $this->assertNull($version->getBuild());
    }
}
