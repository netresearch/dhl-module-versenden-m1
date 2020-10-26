<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Webservice\ResponseData;

use \Dhl\Versenden\Bcs\Api\Webservice\ResponseData\Status\Response as ResponseStatus;
use Dhl\Versenden\Bcs\Api\Webservice\ResponseData\CreateShipment\LabelCollection;

class CreateShipment
{
    /** @var ResponseStatus */
    private $status;
    /** @var LabelCollection */
    private $createdItems;
    /** @var string[] */
    private $shipmentNumbers;

    /**
     * CreateShipment constructor.
     * @param ResponseStatus $status
     * @param LabelCollection $labels
     * @param string[] $shipmentNumbers
     */
    public function __construct(ResponseStatus $status, LabelCollection $labels, array $shipmentNumbers)
    {
        $this->status          = $status;
        $this->createdItems    = $labels;
        $this->shipmentNumbers = $shipmentNumbers;
    }

    /**
     * @return ResponseStatus
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return LabelCollection
     */
    public function getCreatedItems()
    {
        return $this->createdItems;
    }

    /**
     * Obtain sequence number to shipment number mapping.
     *
     * @return \string[]
     */
    public function getShipmentNumbers()
    {
        return $this->shipmentNumbers;
    }

    /**
     * Obtain created shipment number by given sequence number
     *
     * @param string $sequenceNumber
     * @return null|string
     */
    public function getShipmentNumber($sequenceNumber)
    {
        $numbers = $this->getShipmentNumbers();
        if (!isset($numbers[$sequenceNumber])) {
            return null;
        }

        return $numbers[$sequenceNumber];
    }
}
