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
use \Dhl\Versenden\Webservice\RequestData\ShipmentOrder\PackageCollection;
use \Dhl\Versenden\Webservice\RequestData\ShipmentOrder\Package;
/**
 * Dhl_Versenden_Test_Model_Webservice_RequestData_PackageTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Test_Model_Webservice_RequestData_PackageTest
    extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     */
    public function packages()
    {
        $sequenceNumberOne = '303';
        $weightInKGOne = '1.2000';
        $lengthInCMOne = '30';
        $widthInCMOne = '40';
        $heightInCMOne = '50';
        $packageOne = new Package($sequenceNumberOne, $weightInKGOne, $lengthInCMOne, $widthInCMOne, $heightInCMOne);

        $sequenceNumberTwo = '808';
        $weightInKGTwo = '2.1';
        $packageTwo = new Package($sequenceNumberTwo, $weightInKGTwo);

        $collection = new PackageCollection();
        $this->assertCount(0, $collection);

        $collection->addItem($packageOne);
        $this->assertCount(1, $collection);
        $this->assertInstanceOf(Package::class, $collection->getItem($sequenceNumberOne));
        $this->assertEquals($sequenceNumberOne, $collection->getItem($sequenceNumberOne)->getSequenceNumber());
        $this->assertEquals($weightInKGOne, $collection->getItem($sequenceNumberOne)->getWeightInKG());
        $this->assertEquals($lengthInCMOne, $collection->getItem($sequenceNumberOne)->getLengthInCM());
        $this->assertEquals($widthInCMOne, $collection->getItem($sequenceNumberOne)->getWidthInCM());
        $this->assertEquals($heightInCMOne, $collection->getItem($sequenceNumberOne)->getHeightInCM());

        $collection->setItems(array($packageOne, $packageTwo));
        $this->assertCount(2, $collection);
        $this->assertEquals($sequenceNumberTwo, $collection->getItem($sequenceNumberTwo)->getSequenceNumber());
        $this->assertEquals($weightInKGTwo, $collection->getItem($sequenceNumberTwo)->getWeightInKG());
        $this->assertNull($collection->getItem($sequenceNumberTwo)->getLengthInCM());
        $this->assertNull($collection->getItem($sequenceNumberTwo)->getWidthInCM());
        $this->assertNull($collection->getItem($sequenceNumberTwo)->getHeightInCM());

        foreach ($collection as $package) {
            $this->assertInstanceOf(Package::class, $package);
        }

        $this->assertNull($collection->getItem('foo'));
    }
}
