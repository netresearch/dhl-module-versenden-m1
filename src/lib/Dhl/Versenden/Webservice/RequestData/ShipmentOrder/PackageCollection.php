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
 * @package   Dhl\Versenden\Webservice\RequestData
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
namespace Dhl\Versenden\Webservice\RequestData\ShipmentOrder;
/**
 * PackageCollection
 *
 * @category Dhl
 * @package  Dhl\Versenden\Webservice\RequestData
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class PackageCollection implements \IteratorAggregate, \Countable, \JsonSerializable
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
        $this->packages[$package->getSequenceNumber()] = $package;

        return $this;
    }

    /**
     * @param $sequenceNumber
     * @return Package|null
     */
    public function getItem($sequenceNumber)
    {
        if (!isset($this->packages[$sequenceNumber])) {
            return null;
        }

        return $this->packages[$sequenceNumber];
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
