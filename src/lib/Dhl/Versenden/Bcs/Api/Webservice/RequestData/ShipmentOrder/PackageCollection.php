<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder;

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
