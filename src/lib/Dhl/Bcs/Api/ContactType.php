<?php

namespace Dhl\Bcs\Api;

class ContactType
{

    /**
     * @var CommunicationType $Communication
     */
    protected $Communication = null;

    /**
     * @var NativeAddressType $Address
     */
    protected $Address = null;

    /**
     * @var NameType $Name
     */
    protected $Name = null;

    
    public function __construct()
    {
    
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
     * @return \Dhl\Bcs\Api\ContactType
     */
    public function setCommunication($Communication)
    {
      $this->Communication = $Communication;
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
     * @return \Dhl\Bcs\Api\ContactType
     */
    public function setAddress($Address)
    {
      $this->Address = $Address;
      return $this;
    }

    /**
     * @return NameType
     */
    public function getName()
    {
      return $this->Name;
    }

    /**
     * @param NameType $Name
     * @return \Dhl\Bcs\Api\ContactType
     */
    public function setName($Name)
    {
      $this->Name = $Name;
      return $this;
    }

}
