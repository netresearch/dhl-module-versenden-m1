<?php

namespace Dhl\Bcs\Api;

class CancelPickupRequest
{

    /**
     * @var Version $Version
     */
    protected $Version = null;

    /**
     * @var string $BookingConfirmationNumber
     */
    protected $BookingConfirmationNumber = null;

    /**
     * @param Version $Version
     * @param string $BookingConfirmationNumber
     */
    public function __construct($Version, $BookingConfirmationNumber)
    {
      $this->Version = $Version;
      $this->BookingConfirmationNumber = $BookingConfirmationNumber;
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
     * @return \Dhl\Bcs\Api\CancelPickupRequest
     */
    public function setVersion($Version)
    {
      $this->Version = $Version;
      return $this;
    }

    /**
     * @return string
     */
    public function getBookingConfirmationNumber()
    {
      return $this->BookingConfirmationNumber;
    }

    /**
     * @param string $BookingConfirmationNumber
     * @return \Dhl\Bcs\Api\CancelPickupRequest
     */
    public function setBookingConfirmationNumber($BookingConfirmationNumber)
    {
      $this->BookingConfirmationNumber = $BookingConfirmationNumber;
      return $this;
    }

}
