<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Webservice\ResponseData\DeleteShipment;

use Dhl\Versenden\Bcs\Api\Webservice\ResponseData\Status\Item as DeletionState;

class StatusCollection implements \IteratorAggregate, \Countable
{
    /**
     * @var DeletionState[]
     */
    protected $deletionStates = [];

    /**
     * @return int
     */
    public function count()
    {
        return count($this->deletionStates);
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->deletionStates);
    }

    /**
     * Set all status items to the collection.
     *
     * @param DeletionState[] $deletionStates
     * @return $this
     */
    public function setItems(array $deletionStates)
    {
        $this->deletionStates = [];
        foreach ($deletionStates as $deletionState) {
            $this->addItem($deletionState);
        }

        return $this;
    }

    /**
     * Obtain all status items from collection
     *
     * @return DeletionState[]
     */
    public function getItems()
    {
        return $this->deletionStates;
    }

    /**
     * Add a status item to the collection.
     *
     * @param DeletionState $deletionState
     * @return $this
     */
    public function addItem(DeletionState $deletionState)
    {
        $this->deletionStates[$deletionState->getIdentifier()] = $deletionState;

        return $this;
    }

    /**
     * @param $identifier
     * @return DeletionState|null
     */
    public function getItem($identifier)
    {
        if (!isset($this->deletionStates[$identifier])) {
            return null;
        }

        return $this->deletionStates[$identifier];
    }
}
