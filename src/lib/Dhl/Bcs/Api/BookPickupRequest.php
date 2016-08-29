<?php

namespace Dhl\Bcs\Api;

class BookPickupRequest
{

    /**
     * @var Version $Version
     */
    protected $Version = null;

    /**
     * @var PickupBookingInformationType $BookingInformation
     */
    protected $BookingInformation = null;

    /**
     * @var PickupAddressType $PickupAddress
     */
    protected $PickupAddress = null;

    /**
     * @var PickupOrdererType $ContactOrderer
     */
    protected $ContactOrderer = null;

    /**
     * @param Version $Version
     * @param PickupBookingInformationType $BookingInformation
     * @param PickupAddressType $PickupAddress
     * @param PickupOrdererType $ContactOrderer
     */
    public function __construct($Version, $BookingInformation, $PickupAddress, $ContactOrderer)
    {
      $this->Version = $Version;
      $this->BookingInformation = $BookingInformation;
      $this->PickupAddress = $PickupAddress;
      $this->ContactOrderer = $ContactOrderer;
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
     * @return \Dhl\Bcs\Api\BookPickupRequest
     */
    public function setVersion($Version)
    {
      $this->Version = $Version;
      return $this;
    }

    /**
     * @return PickupBookingInformationType
     */
    public function getBookingInformation()
    {
      return $this->BookingInformation;
    }

    /**
     * @param PickupBookingInformationType $BookingInformation
     * @return \Dhl\Bcs\Api\BookPickupRequest
     */
    public function setBookingInformation($BookingInformation)
    {
      $this->BookingInformation = $BookingInformation;
      return $this;
    }

    /**
     * @return PickupAddressType
     */
    public function getPickupAddress()
    {
      return $this->PickupAddress;
    }

    /**
     * @param PickupAddressType $PickupAddress
     * @return \Dhl\Bcs\Api\BookPickupRequest
     */
    public function setPickupAddress($PickupAddress)
    {
      $this->PickupAddress = $PickupAddress;
      return $this;
    }

    /**
     * @return PickupOrdererType
     */
    public function getContactOrderer()
    {
      return $this->ContactOrderer;
    }

    /**
     * @param PickupOrdererType $ContactOrderer
     * @return \Dhl\Bcs\Api\BookPickupRequest
     */
    public function setContactOrderer($ContactOrderer)
    {
      $this->ContactOrderer = $ContactOrderer;
      return $this;
    }

}
