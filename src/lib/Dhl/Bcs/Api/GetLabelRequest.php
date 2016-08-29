<?php

namespace Dhl\Bcs\Api;

class GetLabelRequest
{

    /**
     * @var Version $Version
     */
    protected $Version = null;

    /**
     * @var shipmentNumber $shipmentNumber
     */
    protected $shipmentNumber = null;

    /**
     * @var labelResponseType $labelResponseType
     */
    protected $labelResponseType = null;

    /**
     * @param Version $Version
     * @param shipmentNumber $shipmentNumber
     * @param labelResponseType $labelResponseType
     */
    public function __construct($Version, $shipmentNumber, $labelResponseType)
    {
      $this->Version = $Version;
      $this->shipmentNumber = $shipmentNumber;
      $this->labelResponseType = $labelResponseType;
    }

    /**
     * @return Version
     */
    public function getVersion()
    {
      return $this->Version;
    }

    /**
     * @param Version $Version
     * @return \Dhl\Bcs\Api\GetLabelRequest
     */
    public function setVersion($Version)
    {
      $this->Version = $Version;
      return $this;
    }

    /**
     * @return shipmentNumber
     */
    public function getShipmentNumber()
    {
      return $this->shipmentNumber;
    }

    /**
     * @param shipmentNumber $shipmentNumber
     * @return \Dhl\Bcs\Api\GetLabelRequest
     */
    public function setShipmentNumber($shipmentNumber)
    {
      $this->shipmentNumber = $shipmentNumber;
      return $this;
    }

    /**
     * @return labelResponseType
     */
    public function getLabelResponseType()
    {
      return $this->labelResponseType;
    }

    /**
     * @param labelResponseType $labelResponseType
     * @return \Dhl\Bcs\Api\GetLabelRequest
     */
    public function setLabelResponseType($labelResponseType)
    {
      $this->labelResponseType = $labelResponseType;
      return $this;
    }

}
