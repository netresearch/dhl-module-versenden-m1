<?php

/**
 * See LICENSE.md for license details.
 */

use \Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder\PackageCollection;
use \Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder\Package;

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
        $this->assertEquals($sequenceNumberOne, $collection->getItem($sequenceNumberOne)->getPackageId());
        $this->assertEquals($weightInKGOne, $collection->getItem($sequenceNumberOne)->getWeightInKG());
        $this->assertEquals($lengthInCMOne, $collection->getItem($sequenceNumberOne)->getLengthInCM());
        $this->assertEquals($widthInCMOne, $collection->getItem($sequenceNumberOne)->getWidthInCM());
        $this->assertEquals($heightInCMOne, $collection->getItem($sequenceNumberOne)->getHeightInCM());

        $collection->setItems(array($packageOne, $packageTwo));
        $this->assertCount(2, $collection);
        $this->assertEquals($sequenceNumberTwo, $collection->getItem($sequenceNumberTwo)->getPackageId());
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
