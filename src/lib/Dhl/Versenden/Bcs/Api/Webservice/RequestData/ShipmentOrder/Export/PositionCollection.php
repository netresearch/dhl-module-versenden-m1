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
namespace Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder\Export;
/**
 * PositionCollection
 *
 * @category Dhl
 * @package  Dhl\Versenden\Bcs\Api\Webservice\RequestData
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class PositionCollection implements \IteratorAggregate, \Countable
{
    /**
     * @var Position[]
     */
    protected $positions = [];

    /**
     * @return int
     */
    public function count()
    {
        return count($this->positions);
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->positions);
    }

    /**
     * Set all shipment orders to the collection.
     *
     * @param Position[] $positions
     * @return $this
     */
    public function setItems(array $positions)
    {
        $this->positions = [];
        foreach ($positions as $position) {
            $this->addItem($position);
        }

        return $this;
    }

    /**
     * Obtain all shipment orders from collection
     *
     * @return Position[]
     */
    public function getItems()
    {
        return $this->positions;
    }

    /**
     * Add a shipment order to the collection.
     *
     * @param Position $position
     * @return $this
     */
    public function addItem(Position $position)
    {
        $this->positions[$position->getSequenceNumber()] = $position;

        return $this;
    }

    /**
     * @param $sequenceNumber
     * @return Position|null
     */
    public function getItem($sequenceNumber)
    {
        if (!isset($this->positions[$sequenceNumber])) {
            return null;
        }

        return $this->positions[$sequenceNumber];
    }
}
