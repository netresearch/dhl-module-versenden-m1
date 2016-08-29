<?php

namespace Dhl\Bcs\Api;

class GetExportDocResponse
{

    /**
     * @var Version $Version
     */
    protected $Version = null;

    /**
     * @var Statusinformation $Status
     */
    protected $Status = null;

    /**
     * @var ExportDocData $ExportDocData
     */
    protected $ExportDocData = null;

    /**
     * @param Version $Version
     * @param Statusinformation $Status
     * @param ExportDocData $ExportDocData
     */
    public function __construct($Version, $Status, $ExportDocData)
    {
      $this->Version = $Version;
      $this->Status = $Status;
      $this->ExportDocData = $ExportDocData;
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
     * @return \Dhl\Bcs\Api\GetExportDocResponse
     */
    public function setVersion($Version)
    {
      $this->Version = $Version;
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
     * @return \Dhl\Bcs\Api\GetExportDocResponse
     */
    public function setStatus($Status)
    {
      $this->Status = $Status;
      return $this;
    }

    /**
     * @return ExportDocData
     */
    public function getExportDocData()
    {
      return $this->ExportDocData;
    }

    /**
     * @param ExportDocData $ExportDocData
     * @return \Dhl\Bcs\Api\GetExportDocResponse
     */
    public function setExportDocData($ExportDocData)
    {
      $this->ExportDocData = $ExportDocData;
      return $this;
    }

}
