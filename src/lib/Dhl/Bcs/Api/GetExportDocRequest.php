<?php

namespace Dhl\Bcs\Api;

class GetExportDocRequest
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
     * @var exportDocResponseType $exportDocResponseType
     */
    protected $exportDocResponseType = null;

    /**
     * @param Version $Version
     * @param shipmentNumber $shipmentNumber
     * @param exportDocResponseType $exportDocResponseType
     */
    public function __construct($Version, $shipmentNumber, $exportDocResponseType)
    {
      $this->Version = $Version;
      $this->shipmentNumber = $shipmentNumber;
      $this->exportDocResponseType = $exportDocResponseType;
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
     * @return \Dhl\Bcs\Api\GetExportDocRequest
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
     * @return \Dhl\Bcs\Api\GetExportDocRequest
     */
    public function setShipmentNumber($shipmentNumber)
    {
      $this->shipmentNumber = $shipmentNumber;
      return $this;
    }

    /**
     * @return exportDocResponseType
     */
    public function getExportDocResponseType()
    {
      return $this->exportDocResponseType;
    }

    /**
     * @param exportDocResponseType $exportDocResponseType
     * @return \Dhl\Bcs\Api\GetExportDocRequest
     */
    public function setExportDocResponseType($exportDocResponseType)
    {
      $this->exportDocResponseType = $exportDocResponseType;
      return $this;
    }

}
