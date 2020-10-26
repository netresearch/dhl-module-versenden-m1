<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Webservice\RequestData;

use Dhl\Versenden\Bcs\Api\Webservice\RequestData;

class DeleteShipment extends RequestData
{
    /** @var Version */
    private $version;
    /** @var string[] */
    private $shipmentNumbers;

    /**
     * DeleteShipment constructor.
     * @param Version $version
     * @param \string[] $shipmentNumbers
     */
    public function __construct(Version $version, array $shipmentNumbers)
    {
        $this->version = $version;
        $this->shipmentNumbers = $shipmentNumbers;
    }

    /**
     * @return Version
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return \string[]
     */
    public function getShipmentNumbers()
    {
        return $this->shipmentNumbers;
    }
}
