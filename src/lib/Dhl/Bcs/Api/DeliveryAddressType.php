<?php

namespace Dhl\Bcs\Api;

class DeliveryAddressType
{

    /**
     * @var NativeAddressType $NativeAddress
     */
    protected $NativeAddress = null;

    /**
     * @var PostfilialeType $PostOffice
     */
    protected $PostOffice = null;

    /**
     * @var PackStationType $PackStation
     */
    protected $PackStation = null;

    /**
     * @var streetNameCode $streetNameCode
     */
    protected $streetNameCode = null;

    /**
     * @var streetNumberCode $streetNumberCode
     */
    protected $streetNumberCode = null;

    /**
     * @param NativeAddressType $NativeAddress
     * @param PostfilialeType $PostOffice
     * @param PackStationType $PackStation
     * @param streetNameCode $streetNameCode
     * @param streetNumberCode $streetNumberCode
     */
    public function __construct($NativeAddress, $PostOffice, $PackStation, $streetNameCode, $streetNumberCode)
    {
      $this->NativeAddress = $NativeAddress;
      $this->PostOffice = $PostOffice;
      $this->PackStation = $PackStation;
      $this->streetNameCode = $streetNameCode;
      $this->streetNumberCode = $streetNumberCode;
    }

    /**
     * @return NativeAddressType
     */
    public function getNativeAddress()
    {
      return $this->NativeAddress;
    }

    /**
     * @param NativeAddressType $NativeAddress
     * @return \Dhl\Bcs\Api\DeliveryAddressType
     */
    public function setNativeAddress($NativeAddress)
    {
      $this->NativeAddress = $NativeAddress;
      return $this;
    }

    /**
     * @return PostfilialeType
     */
    public function getPostOffice()
    {
      return $this->PostOffice;
    }

    /**
     * @param PostfilialeType $PostOffice
     * @return \Dhl\Bcs\Api\DeliveryAddressType
     */
    public function setPostOffice($PostOffice)
    {
      $this->PostOffice = $PostOffice;
      return $this;
    }

    /**
     * @return PackStationType
     */
    public function getPackStation()
    {
      return $this->PackStation;
    }

    /**
     * @param PackStationType $PackStation
     * @return \Dhl\Bcs\Api\DeliveryAddressType
     */
    public function setPackStation($PackStation)
    {
      $this->PackStation = $PackStation;
      return $this;
    }

    /**
     * @return streetNameCode
     */
    public function getStreetNameCode()
    {
      return $this->streetNameCode;
    }

    /**
     * @param streetNameCode $streetNameCode
     * @return \Dhl\Bcs\Api\DeliveryAddressType
     */
    public function setStreetNameCode($streetNameCode)
    {
      $this->streetNameCode = $streetNameCode;
      return $this;
    }

    /**
     * @return streetNumberCode
     */
    public function getStreetNumberCode()
    {
      return $this->streetNumberCode;
    }

    /**
     * @param streetNumberCode $streetNumberCode
     * @return \Dhl\Bcs\Api\DeliveryAddressType
     */
    public function setStreetNumberCode($streetNumberCode)
    {
      $this->streetNumberCode = $streetNumberCode;
      return $this;
    }

}
