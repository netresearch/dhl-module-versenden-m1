<?php

namespace Dhl\Bcs\Api;

class GetManifestResponse
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
     * @var base64Binary $manifestData
     */
    protected $manifestData = null;

    /**
     * @param Version $Version
     * @param Statusinformation $Status
     * @param base64Binary $manifestData
     */
    public function __construct($Version, $Status, $manifestData)
    {
      $this->Version = $Version;
      $this->Status = $Status;
      $this->manifestData = $manifestData;
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
     * @return \Dhl\Bcs\Api\GetManifestResponse
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
     * @return \Dhl\Bcs\Api\GetManifestResponse
     */
    public function setStatus($Status)
    {
      $this->Status = $Status;
      return $this;
    }

    /**
     * @return base64Binary
     */
    public function getManifestData()
    {
      return $this->manifestData;
    }

    /**
     * @param base64Binary $manifestData
     * @return \Dhl\Bcs\Api\GetManifestResponse
     */
    public function setManifestData($manifestData)
    {
      $this->manifestData = $manifestData;
      return $this;
    }

}
