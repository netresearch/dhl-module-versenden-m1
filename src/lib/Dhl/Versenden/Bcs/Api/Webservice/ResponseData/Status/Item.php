<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Webservice\ResponseData\Status;

class Item extends Response
{
    /**
     * Status identifier, either sequence number or shipment number.
     * @var string
     */
    protected $identifier;

    /**
     * Status constructor.
     * @param string $identifier
     * @param string $statusCode
     * @param string $statusText
     * @param string[] $statusMessage
     */
    public function __construct($identifier, $statusCode, $statusText, array $statusMessage)
    {
        $this->identifier = $identifier;
        parent::__construct($statusCode, $statusText, $statusMessage);
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }
}
