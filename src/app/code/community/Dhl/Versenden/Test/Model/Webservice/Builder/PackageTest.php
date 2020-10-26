<?php

/**
 * See LICENSE.md for license details.
 */

use \Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder\PackageCollection;
use \Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder\Package;

class Dhl_Versenden_Test_Model_Webservice_Builder_PackageTest
    extends EcomDev_PHPUnit_Test_Case
{
    protected $minWeightInKG = 0.01;

    /**
     * @test
     * @expectedException Mage_Core_Exception
     */
    public function constructorArgUnitOfMeasureMissing()
    {
        $args = array(
            'min_weight' => $this->minWeightInKG,
        );
        Mage::getModel('dhl_versenden/webservice_builder_package', $args);
    }

    /**
     * @test
     * @expectedException Mage_Core_Exception
     */
    public function constructorArgUnitOfMeasureWrongType()
    {
        $args = array(
            'unit_of_measure' => new stdClass(),
            'min_weight'      => $this->minWeightInKG,
        );
        Mage::getModel('dhl_versenden/webservice_builder_package', $args);
    }

    /**
     * @test
     * @expectedException Mage_Core_Exception
     */
    public function constructorArgMinWeightMissing()
    {
        $args = array(
            'unit_of_measure' => 'G',
        );
        Mage::getModel('dhl_versenden/webservice_builder_package', $args);
    }

    /**
     * @test
     * @expectedException Mage_Core_Exception
     */
    public function constructorArgMinWeightWrongType()
    {
        $args = array(
            'unit_of_measure' => 'G',
            'min_weight'      => new stdClass(),
        );
        Mage::getModel('dhl_versenden/webservice_builder_package', $args);
    }

    /**
     * Min weight calculation with default unit KG
     *
     * @test
     */
    public function getPackagesCalcMinWeight()
    {
        $sequenceNumberOne = '303';
        $weightInKGOne = $this->minWeightInKG + 0.05;
        $lengthInCMOne = '30';
        $widthInCMOne = '40';
        $heightInCMOne = '50';

        $sequenceNumberTwo = '808';
        $weightInKGTwo = $this->minWeightInKG - 0.05;

        $packageOne = array(
            'params' => array(
                'weight' => $weightInKGOne,
                'length' => $lengthInCMOne,
                'height' => $heightInCMOne,
                'width'  => $widthInCMOne,
            ),
        );

        $packageTwo = array(
            'params' => array(
                'weight' => $weightInKGTwo,
            ),
        );
        $packageInfo = array(
            $sequenceNumberOne => $packageOne,
            $sequenceNumberTwo => $packageTwo,
        );

        /** @var Dhl_Versenden_Model_Webservice_Builder_Package $builder */
        $args = array(
            'unit_of_measure' => 'KG',
            'min_weight'      => $this->minWeightInKG,
        );
        $builder = Mage::getModel('dhl_versenden/webservice_builder_package', $args);

        $packageCollection = $builder->getPackages($packageInfo);
        $this->assertInstanceOf(PackageCollection::class, $packageCollection);
        $this->assertCount(count($packageInfo), $packageCollection);

        $this->assertInstanceOf(Package::class, $packageCollection->getItem($sequenceNumberOne));
        $this->assertEquals($sequenceNumberOne, $packageCollection->getItem($sequenceNumberOne)->getPackageId());
        $this->assertEquals($weightInKGOne, $packageCollection->getItem($sequenceNumberOne)->getWeightInKG());
        $this->assertEquals($lengthInCMOne, $packageCollection->getItem($sequenceNumberOne)->getLengthInCM());
        $this->assertEquals($widthInCMOne, $packageCollection->getItem($sequenceNumberOne)->getWidthInCM());
        $this->assertEquals($heightInCMOne, $packageCollection->getItem($sequenceNumberOne)->getHeightInCM());

        $this->assertInstanceOf(Package::class, $packageCollection->getItem($sequenceNumberTwo));
        $this->assertEquals($sequenceNumberTwo, $packageCollection->getItem($sequenceNumberTwo)->getPackageId());
        $this->assertEquals($this->minWeightInKG, $packageCollection->getItem($sequenceNumberTwo)->getWeightInKG());
        $this->assertNull($packageCollection->getItem($sequenceNumberTwo)->getLengthInCM());
        $this->assertNull($packageCollection->getItem($sequenceNumberTwo)->getWidthInCM());
        $this->assertNull($packageCollection->getItem($sequenceNumberTwo)->getHeightInCM());
    }

    /**
     * Global weight is configured as KG. May or may not be overridden on packaging level.
     *
     * @test
     */
    public function getPackagesWithWeightUnitKG()
    {
        $args = array('unit_of_measure' => 'KG', 'min_weight' => $this->minWeightInKG);
        $builder = Mage::getModel('dhl_versenden/webservice_builder_package', $args);

        $sequenceNumber = '808';


        // (1) No override on packaging level
        $weightInKG = 0.450;
        $package = array(
            'params' => array(
                'weight' => $weightInKG,
                'weight_units' => 'KG',
            ),
        );
        $packageInfo = array($sequenceNumber => $package);
        $packageCollection = $builder->getPackages($packageInfo);

        $this->assertEquals($weightInKG, $packageCollection->getItem($sequenceNumber)->getWeightInKG());


        // (2) With override on packaging level
        $weightInKG = 450;
        $package = array(
            'params' => array(
                'weight' => $weightInKG,
                'weight_units' => 'G',
            ),
        );
        $packageInfo = array($sequenceNumber => $package);
        $packageCollection = $builder->getPackages($packageInfo);

        $this->assertEquals('0.450', $packageCollection->getItem($sequenceNumber)->getWeightInKG());
    }

    /**
     * Global weight is configured as G. May or may not be overridden on packaging level.
     *
     * @test
     */
    public function getPackagesWithWeightUnitG()
    {
        /** @var Dhl_Versenden_Model_Webservice_Builder_Package $builder */
        $args = array('unit_of_measure' => 'G', 'min_weight' => $this->minWeightInKG);
        $builder = Mage::getModel('dhl_versenden/webservice_builder_package', $args);

        $sequenceNumber = '808';


        // (1) No override on packaging level
        $weightInG = 450.000;
        $package = array(
            'params' => array(
                'weight' => $weightInG,
                'weight_units' => 'G',
            ),
        );
        $packageInfo = array($sequenceNumber => $package);
        $packageCollection = $builder->getPackages($packageInfo);

        $this->assertInstanceOf(PackageCollection::class, $packageCollection);
        $this->assertCount(count($packageInfo), $packageCollection);

        $this->assertInstanceOf(Package::class, $packageCollection->getItem($sequenceNumber));
        $this->assertEquals($sequenceNumber, $packageCollection->getItem($sequenceNumber)->getPackageId());
        $this->assertEquals('0.450', $packageCollection->getItem($sequenceNumber)->getWeightInKG());
        $this->assertNull($packageCollection->getItem($sequenceNumber)->getLengthInCM());
        $this->assertNull($packageCollection->getItem($sequenceNumber)->getWidthInCM());
        $this->assertNull($packageCollection->getItem($sequenceNumber)->getHeightInCM());


        // (2) With override on packaging level
        $weightInKG = 0.450;
        $package = array(
            'params' => array(
                'weight' => $weightInKG,
                'weight_units' => 'KG',
            ),
        );
        $packageInfo = array($sequenceNumber => $package);
        $packageCollection = $builder->getPackages($packageInfo);

        $this->assertEquals($weightInKG, $packageCollection->getItem($sequenceNumber)->getWeightInKG());
    }
}
