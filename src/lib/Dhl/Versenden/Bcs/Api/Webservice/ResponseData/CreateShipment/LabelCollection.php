<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Webservice\ResponseData\CreateShipment;

class LabelCollection implements \IteratorAggregate, \Countable
{
    /**
     * @var Label[]
     */
    protected $labels = [];

    /**
     * @return int
     */
    public function count()
    {
        return count($this->labels);
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->labels);
    }

    /**
     * Set all labels to the collection.
     *
     * @param Label[] $labels
     * @return $this
     */
    public function setItems(array $labels)
    {
        $this->labels = [];
        foreach ($labels as $label) {
            $this->addItem($label);
        }

        return $this;
    }

    /**
     * Obtain all labels from collection
     *
     * @return Label[]
     */
    public function getItems()
    {
        return $this->labels;
    }

    /**
     * Add a label to the collection.
     *
     * @param Label $label
     * @return $this
     */
    public function addItem(Label $label)
    {
        $this->labels[$label->getSequenceNumber()] = $label;

        return $this;
    }

    /**
     * @param string $sequenceNumber
     * @return Label|null
     */
    public function getItem($sequenceNumber)
    {
        if (!isset($this->labels[$sequenceNumber])) {
            return null;
        }

        return $this->labels[$sequenceNumber];
    }
}
