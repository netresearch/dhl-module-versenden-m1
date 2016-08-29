<?php

namespace Dhl\Bcs\Api;

class PickupAddressType
{

    /**
     * @var NameType $Company
     */
    protected $Company = null;

    /**
     * @var NativeAddressType $Address
     */
    protected $Address = null;

    /**
     * @var CommunicationType $Communication
     */
    protected $Communication = null;

    /**
     * @param NameType $Company
     * @param NativeAddressType $Address
     * @param CommunicationType $Communication
     */
    public function __construct($Company, $Address, $Communication)
    {
      $this->Company = $Company;
      $this->Address = $Address;
      $this->Communication = $Communication;
    }

    /**
     * @return NameType
     */
    public function getCompany()
    {
      return $this->Company;
    }

    /**
     * @param NameType $Company
     * @return \Dhl\Bcs\Api\PickupAddressType
     */
    public function setCompany($Company)
    {
      $this->Company = $Company;
      return $this;
    }

    /**
     * @return NativeAddressType
     */
    public function getAddress()
    {
      return $this->Address;
    }

    /**
     * @param NativeAddressType $Address
     * @return \Dhl\Bcs\Api\PickupAddressType
     */
    public function setAddress($Address)
    {
      $this->Address = $Address;
      return $this;
    }

    /**
     * @return CommunicationType
     */
    public function getCommunication()
    {
      return $this->Communication;
    }

    /**
     * @param CommunicationType $Communication
     * @return \Dhl\Bcs\Api\PickupAddressType
     */
    public function setCommunication($Communication)
    {
      $this->Communication = $Communication;
      return $this;
    }

}
