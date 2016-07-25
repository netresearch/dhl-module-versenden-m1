<?php

namespace Dhl\Bcs\Api;

class BookPickupResponse
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
     * @var string $ConfirmationNumber
     */
    protected $ConfirmationNumber = null;

    /**
     * @var string $ShipmentNumber
     */
    protected $ShipmentNumber = null;

    /**
     * @param Version $Version
     * @param Statusinformation $Status
     * @param string $ConfirmationNumber
     * @param string $ShipmentNumber
     */
    public function __construct($Version, $Status, $ConfirmationNumber, $ShipmentNumber)
    {
      $this->Version = $Version;
      $this->Status = $Status;
      $this->ConfirmationNumber = $ConfirmationNumber;
      $this->ShipmentNumber = $ShipmentNumber;
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
     * @return \Dhl\Bcs\Api\BookPickupResponse
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
     * @return \Dhl\Bcs\Api\BookPickupResponse
     */
    public function setStatus($Status)
    {
      $this->Status = $Status;
      return $this;
    }

    /**
     * @return string
     */
    public function getConfirmationNumber()
    {
      return $this->ConfirmationNumber;
    }

    /**
     * @param string $ConfirmationNumber
     * @return \Dhl\Bcs\Api\BookPickupResponse
     */
    public function setConfirmationNumber($ConfirmationNumber)
    {
      $this->ConfirmationNumber = $ConfirmationNumber;
      return $this;
    }

    /**
     * @return string
     */
    public function getShipmentNumber()
    {
      return $this->ShipmentNumber;
    }

    /**
     * @param string $ShipmentNumber
     * @return \Dhl\Bcs\Api\BookPickupResponse
     */
    public function setShipmentNumber($ShipmentNumber)
    {
      $this->ShipmentNumber = $ShipmentNumber;
      return $this;
    }

}
