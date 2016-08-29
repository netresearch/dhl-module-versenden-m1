<?php

namespace Dhl\Bcs\Api;

class PackstationType
{

    /**
     * @var string $PackstationNumber
     */
    protected $PackstationNumber = null;

    /**
     * @var string $PostNumber
     */
    protected $PostNumber = null;

    /**
     * @var Zip $Zip
     */
    protected $Zip = null;

    /**
     * @var City $City
     */
    protected $City = null;

    /**
     * @param string $PackstationNumber
     * @param string $PostNumber
     * @param Zip $Zip
     * @param City $City
     */
    public function __construct($PackstationNumber, $PostNumber, $Zip, $City)
    {
      $this->PackstationNumber = $PackstationNumber;
      $this->PostNumber = $PostNumber;
      $this->Zip = $Zip;
      $this->City = $City;
    }

    /**
     * @return string
     */
    public function getPackstationNumber()
    {
      return $this->PackstationNumber;
    }

    /**
     * @param string $PackstationNumber
     * @return \Dhl\Bcs\Api\PackstationType
     */
    public function setPackstationNumber($PackstationNumber)
    {
      $this->PackstationNumber = $PackstationNumber;
      return $this;
    }

    /**
     * @return string
     */
    public function getPostNumber()
    {
      return $this->PostNumber;
    }

    /**
     * @param string $PostNumber
     * @return \Dhl\Bcs\Api\PackstationType
     */
    public function setPostNumber($PostNumber)
    {
      $this->PostNumber = $PostNumber;
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
     * @return \Dhl\Bcs\Api\PackstationType
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
     * @return \Dhl\Bcs\Api\PackstationType
     */
    public function setCity($City)
    {
      $this->City = $City;
      return $this;
    }

}
