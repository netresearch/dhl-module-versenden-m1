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
 * DocumentCollection
 *
 * @category Dhl
 * @package  Dhl\Versenden\Bcs\Api\Webservice\RequestData
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
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
