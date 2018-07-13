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
 * @package   Dhl\Versenden\Bcs\Api\Service
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
namespace Dhl\Versenden\Bcs\Api\Shipment\Service;
use \Dhl\Versenden\Bcs\Api\Shipment\Service\Type\Generic as Service;
/**
 * Collection
 *
 * @category Dhl
 * @package  Dhl\Versenden\Bcs\Api\Service
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Collection implements \IteratorAggregate, \Countable
{
    /** @var Service[] */
    protected $services = [];

    /**
     * Collection constructor.
     * @param Service[] $services
     */
    public function __construct(array $services = [])
    {
        $this->setItems($services);
    }

    /**
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return \Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->services);
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return count($this->services);
    }

    /**
     * Set all services to the collection.
     *
     * @param Service[] $services
     * @return $this
     */
    public function setItems(array $services)
    {
        $this->services = [];
        foreach ($services as $service) {
            $this->addItem($service);
        }

        return $this;
    }

    /**
     * @return Service[]
     */
    public function getItems()
    {
        return $this->services;
    }

    /**
     * Add a service to the collection.
     *
     * @param Service $service
     * @return $this
     */
    public function addItem(Service $service)
    {
        $this->services[$service->getCode()] = $service;

        return $this;
    }

    /**
     * @param $serviceCode
     * @return Service|null
     */
    public function getItem($serviceCode)
    {
        if (!isset($this->services[$serviceCode])) {
            return null;
        }

        return $this->services[$serviceCode];
    }

    /**
     * @param $serviceCode
     * @return $this
     */
    public function removeItem($serviceCode)
    {
        unset($this->services[$serviceCode]);
        return $this;
    }
}
