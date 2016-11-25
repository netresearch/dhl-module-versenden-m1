<?php

namespace Dhl\Versenden\Bcs\Soap;

class ReceiverNativeAddressType
{

    /**
     * @var name2 $name2
     */
    protected $name2 = null;

    /**
     * @var name3 $name3
     */
    protected $name3 = null;

    /**
     * @var streetName $streetName
     */
    protected $streetName = null;

    /**
     * @var streetNumber $streetNumber
     */
    protected $streetNumber = null;

    /**
     * @var addressAddition[] $addressAddition
     */
    protected $addressAddition = null;

    /**
     * @var dispatchingInformation $dispatchingInformation
     */
    protected $dispatchingInformation = null;

    /**
     * @var ZipType $zip
     */
    protected $zip = null;

    /**
     * @var city $city
     */
    protected $city = null;

    /**
     * @var CountryType $Origin
     */
    protected $Origin = null;

    /**
     * @param name2 $name2
     * @param name3 $name3
     * @param streetName $streetName
     * @param streetNumber $streetNumber
     * @param ZipType $zip
     * @param city $city
     * @param CountryType $Origin
     */
    public function __construct($name2, $name3, $streetName, $streetNumber, $zip, $city, $Origin)
    {
      $this->name2 = $name2;
      $this->name3 = $name3;
      $this->streetName = $streetName;
      $this->streetNumber = $streetNumber;
      $this->zip = $zip;
      $this->city = $city;
      $this->Origin = $Origin;
    }

    /**
     * @return name2
     */
    public function getName2()
    {
      return $this->name2;
    }

    /**
     * @param name2 $name2
     * @return \Dhl\Versenden\Bcs\Soap\ReceiverNativeAddressType
     */
    public function setName2($name2)
    {
      $this->name2 = $name2;
      return $this;
    }

    /**
     * @return name3
     */
    public function getName3()
    {
      return $this->name3;
    }

    /**
     * @param name3 $name3
     * @return \Dhl\Versenden\Bcs\Soap\ReceiverNativeAddressType
     */
    public function setName3($name3)
    {
      $this->name3 = $name3;
      return $this;
    }

    /**
     * @return streetName
     */
    public function getStreetName()
    {
      return $this->streetName;
    }

    /**
     * @param streetName $streetName
     * @return \Dhl\Versenden\Bcs\Soap\ReceiverNativeAddressType
     */
    public function setStreetName($streetName)
    {
      $this->streetName = $streetName;
      return $this;
    }

    /**
     * @return streetNumber
     */
    public function getStreetNumber()
    {
      return $this->streetNumber;
    }

    /**
     * @param streetNumber $streetNumber
     * @return \Dhl\Versenden\Bcs\Soap\ReceiverNativeAddressType
     */
    public function setStreetNumber($streetNumber)
    {
      $this->streetNumber = $streetNumber;
      return $this;
    }

    /**
     * @return addressAddition[]
     */
    public function getAddressAddition()
    {
      return $this->addressAddition;
    }

    /**
     * @param addressAddition[] $addressAddition
     * @return \Dhl\Versenden\Bcs\Soap\ReceiverNativeAddressType
     */
    public function setAddressAddition(array $addressAddition = null)
    {
      $this->addressAddition = $addressAddition;
      return $this;
    }

    /**
     * @return dispatchingInformation
     */
    public function getDispatchingInformation()
    {
      return $this->dispatchingInformation;
    }

    /**
     * @param dispatchingInformation $dispatchingInformation
     * @return \Dhl\Versenden\Bcs\Soap\ReceiverNativeAddressType
     */
    public function setDispatchingInformation($dispatchingInformation)
    {
      $this->dispatchingInformation = $dispatchingInformation;
      return $this;
    }

    /**
     * @return ZipType
     */
    public function getZip()
    {
      return $this->zip;
    }

    /**
     * @param ZipType $zip
     * @return \Dhl\Versenden\Bcs\Soap\ReceiverNativeAddressType
     */
    public function setZip($zip)
    {
      $this->zip = $zip;
      return $this;
    }

    /**
     * @return city
     */
    public function getCity()
    {
      return $this->city;
    }

    /**
     * @param city $city
     * @return \Dhl\Versenden\Bcs\Soap\ReceiverNativeAddressType
     */
    public function setCity($city)
    {
      $this->city = $city;
      return $this;
    }

    /**
     * @return CountryType
     */
    public function getOrigin()
    {
      return $this->Origin;
    }

    /**
     * @param CountryType $Origin
     * @return \Dhl\Versenden\Bcs\Soap\ReceiverNativeAddressType
     */
    public function setOrigin($Origin)
    {
      $this->Origin = $Origin;
      return $this;
    }

}
