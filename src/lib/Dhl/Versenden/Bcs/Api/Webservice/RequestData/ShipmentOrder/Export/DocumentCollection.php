<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder\Export;

class DocumentCollection implements \IteratorAggregate, \Countable
{
    /**
     * @var Document[]
     */
    protected $documents = [];

    /**
     * @return int
     */
    public function count()
    {
        return count($this->documents);
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->documents);
    }

    /**
     * Set all shipment orders to the collection.
     *
     * @param Document[] $documents
     * @return $this
     */
    public function setItems(array $documents)
    {
        $this->documents = [];
        foreach ($documents as $document) {
            $this->addItem($document);
        }

        return $this;
    }

    /**
     * Obtain all shipment orders from collection
     *
     * @return Document[]
     */
    public function getItems()
    {
        return $this->documents;
    }

    /**
     * Add a shipment order to the collection.
     *
     * @param Document $document
     * @return $this
     */
    public function addItem(Document $document)
    {
        $this->documents[$document->getPackageId()] = $document;

        return $this;
    }

    /**
     * @param int $packageId
     * @return Document|null
     */
    public function getItem($packageId)
    {
        if (!isset($this->documents[$packageId])) {
            return null;
        }

        return $this->documents[$packageId];
    }
}
