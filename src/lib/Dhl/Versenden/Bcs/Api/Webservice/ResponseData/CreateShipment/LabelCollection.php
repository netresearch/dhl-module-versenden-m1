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
namespace Dhl\Versenden\Bcs\Api\Webservice\ResponseData\CreateShipment;
/**
 * LabelCollection
 *
 * @category Dhl
 * @package  Dhl\Versenden\Bcs\Api\Webservice\ResponseData
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
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
