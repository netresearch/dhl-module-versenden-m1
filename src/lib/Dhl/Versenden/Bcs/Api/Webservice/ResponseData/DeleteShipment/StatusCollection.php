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
 * @package   Dhl\Versenden\Bcs\Api\Webservice\ResponseData
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
namespace Dhl\Versenden\Bcs\Api\Webservice\ResponseData\DeleteShipment;
use Dhl\Versenden\Bcs\Api\Webservice\ResponseData\Status\Item as DeletionState;

/**
 * StatusCollection
 *
 * @category Dhl
 * @package  Dhl\Versenden\Bcs\Api\Webservice\ResponseData
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
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
