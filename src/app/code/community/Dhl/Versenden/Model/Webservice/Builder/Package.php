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
use \Dhl\Versenden\Webservice\RequestData\ShipmentOrder;
/**
 * Dhl_Versenden_Model_Webservice_Builder_Package
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Model_Webservice_Builder_Package
{
    /** @var string */
    protected $unitOfMeasure;

    /** @var float */
    protected $minWeight;

    /**
     * Dhl_Versenden_Model_Webservice_Builder_Package constructor.
     * @param mixed[] $args
     * @throws Mage_Core_Exception
     */
    public function __construct($args)
    {
        $argName = 'unit_of_measure';
        if (!isset($args[$argName])) {
            throw new Mage_Core_Exception("required argument missing: $argName");
        }
        if (!is_string($args[$argName])) {
            throw new Mage_Core_Exception("invalid argument: $argName");
        }
        $this->unitOfMeasure = $args[$argName];

        $argName = 'min_weight';
        if (!isset($args[$argName])) {
            throw new Mage_Core_Exception("required argument missing: $argName");
        }
        if (!is_numeric($args[$argName])) {
            throw new Mage_Core_Exception("invalid argument: $argName");
        }
        $this->minWeight = $args[$argName];

        if ($this->unitOfMeasure == 'G') {
            $this->minWeight *= 1000;
        }
    }

    /**
     * @param mixed[] $packageInfo
     * @return ShipmentOrder\PackageCollection
     */
    public function getPackages(array $packageInfo)
    {
        $packageCollection = new ShipmentOrder\PackageCollection();

        foreach ($packageInfo as $idx => $packageDetails) {
            $lenghtInCM = null;
            if (isset($packageDetails['params']['length']) && $packageDetails['params']['length']) {
                $lenghtInCM = $packageDetails['params']['length'];
            }
            $widthInCM = null;
            if (isset($packageDetails['params']['width']) && $packageDetails['params']['width']) {
                $widthInCM = $packageDetails['params']['width'];
            }
            $heightInCM = null;
            if (isset($packageDetails['params']['height']) && $packageDetails['params']['height']) {
                $heightInCM = $packageDetails['params']['height'];
            }

            $package = new ShipmentOrder\Package(
                $idx,
                max($packageDetails['params']['weight'], $this->minWeight),
                $lenghtInCM,
                $widthInCM,
                $heightInCM
            );

            $packageCollection->addItem($package);
        }

        return $packageCollection;
    }
}
