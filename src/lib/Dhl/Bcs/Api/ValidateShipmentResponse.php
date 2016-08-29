<?php

namespace Dhl\Bcs\Api;

class ValidateShipmentResponse
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
     * @var ValidationState $ValidationState
     */
    protected $ValidationState = null;

    /**
     * @param Version $Version
     * @param Statusinformation $Status
     * @param ValidationState $ValidationState
     */
    public function __construct($Version, $Status, $ValidationState)
    {
      $this->Version = $Version;
      $this->Status = $Status;
      $this->ValidationState = $ValidationState;
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
     * @return \Dhl\Bcs\Api\ValidateShipmentResponse
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
     * @return \Dhl\Bcs\Api\ValidateShipmentResponse
     */
    public function setStatus($Status)
    {
      $this->Status = $Status;
      return $this;
    }

    /**
     * @return ValidationState
     */
    public function getValidationState()
    {
      return $this->ValidationState;
    }

    /**
     * @param ValidationState $ValidationState
     * @return \Dhl\Bcs\Api\ValidateShipmentResponse
     */
    public function setValidationState($ValidationState)
    {
      $this->ValidationState = $ValidationState;
      return $this;
    }

}
