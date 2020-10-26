<?php

/**
 * See LICENSE.md for license details.
 */

use \Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder;

class Dhl_Versenden_Model_Webservice_Builder_Package
{
    /** @var string */
    protected $_unitOfMeasure;

    /** @var float */
    protected $_minWeightInKG;

    /**
     * Dhl_Versenden_Model_Webservice_Builder_Package constructor.
     * @param mixed[] $args
     * @throws Mage_Core_Exception
     */
    public function __construct($args)
    {
        $argName = 'unit_of_measure';
        if (!isset($args[$argName])) {
            Mage::throwException("required argument missing: $argName");
        }

        if (!is_string($args[$argName])) {
            Mage::throwException("invalid argument: $argName");
        }

        $this->_unitOfMeasure = $args[$argName];

        $argName = 'min_weight';
        if (!isset($args[$argName])) {
            Mage::throwException("required argument missing: $argName");
        }

        if (!is_numeric($args[$argName])) {
            Mage::throwException("invalid argument: $argName");
        }

        $this->_minWeightInKG = $args[$argName];
    }

    /**
     * @param mixed[] $packageInfo
     * @return ShipmentOrder\PackageCollection
     */
    public function getPackages(array $packageInfo)
    {
        $packageCollection = new ShipmentOrder\PackageCollection();

        foreach ($packageInfo as $id => $packageDetails) {
            $lengthInCM = null;
            if (isset($packageDetails['params']['length']) && $packageDetails['params']['length']) {
                $lengthInCM = $packageDetails['params']['length'];
            }

            $widthInCM = null;
            if (isset($packageDetails['params']['width']) && $packageDetails['params']['width']) {
                $widthInCM = $packageDetails['params']['width'];
            }

            $heightInCM = null;
            if (isset($packageDetails['params']['height']) && $packageDetails['params']['height']) {
                $heightInCM = $packageDetails['params']['height'];
            }

            $weightUnit = $this->_unitOfMeasure;
            if (isset($packageDetails['params']['weight_units']) && $packageDetails['params']['weight_units']) {
                $weightUnit = $packageDetails['params']['weight_units'];
            }

            $weightInKG = $packageDetails['params']['weight'];
            if ($weightUnit == 'G') {
                $weightInKG *= 0.001;
            }

            $weightInKG = max($weightInKG, $this->_minWeightInKG);

            $package = new ShipmentOrder\Package($id, $weightInKG, $lengthInCM, $widthInCM, $heightInCM);
            $packageCollection->addItem($package);
        }

        return $packageCollection;
    }
}
