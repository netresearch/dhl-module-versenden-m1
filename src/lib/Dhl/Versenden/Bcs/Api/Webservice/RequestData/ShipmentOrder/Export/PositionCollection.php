<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder\Export;

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
