<?php

namespace Dhl\Bcs\Api;

class ParcelShopType
{

    /**
     * @var string $ParcelShopNumber
     */
    protected $ParcelShopNumber = null;

    /**
     * @var streetName $streetName
     */
    protected $streetName = null;

    /**
     * @var streetNumber $streetNumber
     */
    protected $streetNumber = null;

    /**
     * @var Zip $Zip
     */
    protected $Zip = null;

    /**
     * @var City $City
     */
    protected $City = null;

    /**
     * @param string $ParcelShopNumber
     * @param Zip $Zip
     * @param City $City
     */
    public function __construct($ParcelShopNumber, $Zip, $City)
    {
      $this->ParcelShopNumber = $ParcelShopNumber;
      $this->Zip = $Zip;
      $this->City = $City;
    }

    /**
     * @return string
     */
    public function getParcelShopNumber()
    {
      return $this->ParcelShopNumber;
    }

    /**
     * @param string $ParcelShopNumber
     * @return \Dhl\Bcs\Api\ParcelShopType
     */
    public function setParcelShopNumber($ParcelShopNumber)
    {
      $this->ParcelShopNumber = $ParcelShopNumber;
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
     * @return \Dhl\Bcs\Api\ParcelShopType
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
     * @return \Dhl\Bcs\Api\ParcelShopType
     */
    public function setStreetNumber($streetNumber)
    {
      $this->streetNumber = $streetNumber;
      return $this;
    }

    /**
     * @return Zip
     */
    public function getZip()
    {
      return $this->Zip;
    }

    /**
     * @param Zip $Zip
     * @return \Dhl\Bcs\Api\ParcelShopType
     */
    public function setZip($Zip)
    {
      $this->Zip = $Zip;
      return $this;
    }

    /**
     * @return City
     */
    public function getCity()
    {
      return $this->City;
    }

    /**
     * @param City $City
     * @return \Dhl\Bcs\Api\ParcelShopType
     */
    public function setCity($City)
    {
      $this->City = $City;
      return $this;
    }

}
