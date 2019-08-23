<?php

namespace Dhl\Versenden\Bcs\Soap;

class ShipperTypeType
{

    /**
     * @var NameType $Name
     */
    protected $Name = null;

    /**
     * @var NativeAddressType $Address
     */
    protected $Address = null;

    /**
     * @var CommunicationType $Communication
     */
    protected $Communication = null;

    /**
     * @param NameType $Name
     * @param NativeAddressType $Address
     */
    public function __construct($Name, $Address)
    {
      $this->Name = $Name;
      $this->Address = $Address;
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
     * @return \Dhl\Versenden\Bcs\Soap\ShipperTypeType
     */
    public function setName($Name)
    {
      $this->Name = $Name;
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
     * @return \Dhl\Versenden\Bcs\Soap\ShipperTypeType
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
     * @return \Dhl\Versenden\Bcs\Soap\ShipperTypeType
     */
    public function setCommunication($Communication)
    {
      $this->Communication = $Communication;
      return $this;
    }

}
