<?php

namespace Dhl\Versenden\Bcs\Soap;

class ReceiverTypeType
{

    /**
     * @var name1 $name1
     */
    protected $name1 = null;

    /**
     * @var ReceiverNativeAddressType $Address
     */
    protected $Address = null;

    /**
     * @var PackStationType $Packstation
     */
    protected $Packstation = null;

    /**
     * @var PostfilialeType $Postfiliale
     */
    protected $Postfiliale = null;

    /**
     * @var ParcelShopType $ParcelShop
     */
    protected $ParcelShop = null;

    /**
     * @var CommunicationType $Communication
     */
    protected $Communication = null;

    /**
     * @param name1 $name1
     * @param ReceiverNativeAddressType $Address
     * @param PackStationType $Packstation
     * @param PostfilialeType $Postfiliale
     * @param ParcelShopType $ParcelShop
     * @param CommunicationType $Communication
     */
    public function __construct($name1, $Address, $Packstation, $Postfiliale, $ParcelShop, $Communication)
    {
      $this->name1 = $name1;
      $this->Address = $Address;
      $this->Packstation = $Packstation;
      $this->Postfiliale = $Postfiliale;
      $this->ParcelShop = $ParcelShop;
      $this->Communication = $Communication;
    }

    /**
     * @return name1
     */
    public function getName1()
    {
      return $this->name1;
    }

    /**
     * @param name1 $name1
     * @return \Dhl\Versenden\Bcs\Soap\ReceiverTypeType
     */
    public function setName1($name1)
    {
      $this->name1 = $name1;
      return $this;
    }

    /**
     * @return ReceiverNativeAddressType
     */
    public function getAddress()
    {
      return $this->Address;
    }

    /**
     * @param ReceiverNativeAddressType $Address
     * @return \Dhl\Versenden\Bcs\Soap\ReceiverTypeType
     */
    public function setAddress($Address)
    {
      $this->Address = $Address;
      return $this;
    }

    /**
     * @return PackStationType
     */
    public function getPackstation()
    {
      return $this->Packstation;
    }

    /**
     * @param PackStationType $Packstation
     * @return \Dhl\Versenden\Bcs\Soap\ReceiverTypeType
     */
    public function setPackstation($Packstation)
    {
      $this->Packstation = $Packstation;
      return $this;
    }

    /**
     * @return PostfilialeType
     */
    public function getPostfiliale()
    {
      return $this->Postfiliale;
    }

    /**
     * @param PostfilialeType $Postfiliale
     * @return \Dhl\Versenden\Bcs\Soap\ReceiverTypeType
     */
    public function setPostfiliale($Postfiliale)
    {
      $this->Postfiliale = $Postfiliale;
      return $this;
    }

    /**
     * @return ParcelShopType
     */
    public function getParcelShop()
    {
      return $this->ParcelShop;
    }

    /**
     * @param ParcelShopType $ParcelShop
     * @return \Dhl\Versenden\Bcs\Soap\ReceiverTypeType
     */
    public function setParcelShop($ParcelShop)
    {
      $this->ParcelShop = $ParcelShop;
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
     * @return \Dhl\Versenden\Bcs\Soap\ReceiverTypeType
     */
    public function setCommunication($Communication)
    {
      $this->Communication = $Communication;
      return $this;
    }

}
