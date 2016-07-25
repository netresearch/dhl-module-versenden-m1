<?php

namespace Dhl\Bcs\Api;

class ExportDocData
{

    /**
     * @var shipmentNumber $shipmentNumber
     */
    protected $shipmentNumber = null;

    /**
     * @var Statusinformation $Status
     */
    protected $Status = null;

    /**
     * @var base64Binary $exportDocData
     */
    protected $exportDocData = null;

    /**
     * @var string $exportDocURL
     */
    protected $exportDocURL = null;

    /**
     * @param shipmentNumber $shipmentNumber
     * @param Statusinformation $Status
     */
    public function __construct($shipmentNumber, $Status)
    {
      $this->shipmentNumber = $shipmentNumber;
      $this->Status = $Status;
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
     * @return \Dhl\Bcs\Api\ExportDocData
     */
    public function setShipmentNumber($shipmentNumber)
    {
      $this->shipmentNumber = $shipmentNumber;
      return $this;
    }

    /**
     * @return Statusinformation
     */
    public function getStatus()
    {
      return $this->Status;
    }

    /**
     * @param Statusinformation $Status
     * @return \Dhl\Bcs\Api\ExportDocData
     */
    public function setStatus($Status)
    {
      $this->Status = $Status;
      return $this;
    }

    /**
     * @return base64Binary
     */
    public function getExportDocData()
    {
      return $this->exportDocData;
    }

    /**
     * @param base64Binary $exportDocData
     * @return \Dhl\Bcs\Api\ExportDocData
     */
    public function setExportDocData($exportDocData)
    {
      $this->exportDocData = $exportDocData;
      return $this;
    }

    /**
     * @return string
     */
    public function getExportDocURL()
    {
      return $this->exportDocURL;
    }

    /**
     * @param string $exportDocURL
     * @return \Dhl\Bcs\Api\ExportDocData
     */
    public function setExportDocURL($exportDocURL)
    {
      $this->exportDocURL = $exportDocURL;
      return $this;
    }

}
