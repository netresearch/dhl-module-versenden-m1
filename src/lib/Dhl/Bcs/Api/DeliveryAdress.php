<?php

namespace Dhl\Bcs\Api;

class DeliveryAdress
{

    /**
     * @var NameType $Company
     */
    protected $Company = null;

    /**
     * @var string $Name3
     */
    protected $Name3 = null;

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
     * @param string $Name3
     * @param NativeAddressType $Address
     * @param CommunicationType $Communication
     */
    public function __construct($Company, $Name3, $Address, $Communication)
    {
      $this->Company = $Company;
      $this->Name3 = $Name3;
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
     * @return \Dhl\Bcs\Api\DeliveryAdress
     */
    public function setCompany($Company)
    {
      $this->Company = $Company;
      return $this;
    }

    /**
     * @return string
     */
    public function getName3()
    {
      return $this->Name3;
    }

    /**
     * @param string $Name3
     * @return \Dhl\Bcs\Api\DeliveryAdress
     */
    public function setName3($Name3)
    {
      $this->Name3 = $Name3;
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
     * @return \Dhl\Bcs\Api\DeliveryAdress
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
     * @return \Dhl\Bcs\Api\DeliveryAdress
     */
    public function setCommunication($Communication)
    {
      $this->Communication = $Communication;
      return $this;
    }

}
