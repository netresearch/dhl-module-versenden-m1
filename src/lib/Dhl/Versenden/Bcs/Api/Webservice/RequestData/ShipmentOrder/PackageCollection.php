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
 * @package   Dhl\Versenden\Bcs\Api\Webservice\RequestData
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
namespace Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder;
/**
 * PackageCollection
 *
 * @category Dhl
 * @package  Dhl\Versenden\Bcs\Api\Webservice\RequestData
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class PackageCollection implements \IteratorAggregate, \Countable
{
    /**
     * @var Package[]
     */
    protected $packages = [];

    /**
     * @return int
     */
    public function count()
    {
        return count($this->packages);
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->packages);
    }

    /**
     * Set all shipment orders to the collection.
     *
     * @param Package[] $packages
     * @return $this
     */
    public function setItems(array $packages)
    {
        $this->packages = [];
        foreach ($packages as $package) {
            $this->addItem($package);
        }

        return $this;
    }

    /**
     * Obtain all shipment orders from collection
     *
     * @return Package[]
     */
    public function getItems()
    {
        return $this->packages;
    }

    /**
     * Add a shipment order to the collection.
     *
     * @param Package $package
     * @return $this
     */
    public function addItem(Package $package)
    {
        $this->packages[$package->getPackageId()] = $package;

        return $this;
    }

    /**
     * @param $packageId
     * @return Package|null
     */
    public function getItem($packageId)
    {
        if (!isset($this->packages[$packageId])) {
            return null;
        }

        return $this->packages[$packageId];
    }
}
