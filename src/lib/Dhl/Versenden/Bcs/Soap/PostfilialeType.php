<?php

namespace Dhl\Versenden\Bcs\Soap;

class PostfilialeType
{

    /**
     * @var string $PostfilialNumber
     */
    protected $PostfilialNumber = null;

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
     * @param string $PostfilialNumber
     * @param string $PostNumber
     * @param Zip $Zip
     * @param City $City
     */
    public function __construct($PostfilialNumber, $PostNumber, $Zip, $City)
    {
      $this->PostfilialNumber = $PostfilialNumber;
      $this->PostNumber = $PostNumber;
      $this->Zip = $Zip;
      $this->City = $City;
    }

    /**
     * @return string
     */
    public function getPostfilialNumber()
    {
      return $this->PostfilialNumber;
    }

    /**
     * @param string $PostfilialNumber
     * @return \Dhl\Versenden\Bcs\Soap\PostfilialeType
     */
    public function setPostfilialNumber($PostfilialNumber)
    {
      $this->PostfilialNumber = $PostfilialNumber;
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
     * @return \Dhl\Versenden\Bcs\Soap\PostfilialeType
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
     * @return \Dhl\Versenden\Bcs\Soap\PostfilialeType
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
     * @return \Dhl\Versenden\Bcs\Soap\PostfilialeType
     */
    public function setCity($City)
    {
      $this->City = $City;
      return $this;
    }

}
