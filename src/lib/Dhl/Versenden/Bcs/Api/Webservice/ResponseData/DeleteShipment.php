<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Webservice\ResponseData;

use Dhl\Versenden\Bcs\Api\Webservice\ResponseData\Status\Response as ResponseStatus;
use Dhl\Versenden\Bcs\Api\Webservice\ResponseData\DeleteShipment\StatusCollection;

class DeleteShipment
{
    /** @var ResponseStatus */
    private $status;
    /** @var StatusCollection */
    private $deletedItems;

    /**
     * DeleteShipment constructor.
     * @param ResponseStatus $status
     * @param StatusCollection $deletedItems
     */
    public function __construct(ResponseStatus $status, StatusCollection $deletedItems)
    {
        $this->status = $status;
        $this->deletedItems = $deletedItems;
    }

    /**
     * @return ResponseStatus
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return StatusCollection
     */
    public function getDeletedItems()
    {
        return $this->deletedItems;
    }
}
