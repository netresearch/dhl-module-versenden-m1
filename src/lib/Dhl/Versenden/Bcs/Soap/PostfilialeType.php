<?php

namespace Dhl\Versenden\Bcs\Soap;

class PostfilialeType
{

    /**
     * @var postfilialNumber $postfilialNumber
     */
    protected $postfilialNumber = null;

    /**
     * @var postNumber $postNumber
     */
    protected $postNumber = null;

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
     * @param postfilialNumber $postfilialNumber
     * @param postNumber $postNumber
     * @param ZipType $zip
     * @param city $city
     * @param CountryType $Origin
     */
    public function __construct($postfilialNumber, $postNumber, $zip, $city, $Origin)
    {
      $this->postfilialNumber = $postfilialNumber;
      $this->postNumber = $postNumber;
      $this->zip = $zip;
      $this->city = $city;
      $this->Origin = $Origin;
    }

    /**
     * @return postfilialNumber
     */
    public function getPostfilialNumber()
    {
      return $this->postfilialNumber;
    }

    /**
     * @param postfilialNumber $postfilialNumber
     * @return \Dhl\Versenden\Bcs\Soap\PostfilialeType
     */
    public function setPostfilialNumber($postfilialNumber)
    {
      $this->postfilialNumber = $postfilialNumber;
      return $this;
    }

    /**
     * @return postNumber
     */
    public function getPostNumber()
    {
      return $this->postNumber;
    }

    /**
     * @param postNumber $postNumber
     * @return \Dhl\Versenden\Bcs\Soap\PostfilialeType
     */
    public function setPostNumber($postNumber)
    {
      $this->postNumber = $postNumber;
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
     * @return \Dhl\Versenden\Bcs\Soap\PostfilialeType
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
     * @return \Dhl\Versenden\Bcs\Soap\PostfilialeType
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
     * @return \Dhl\Versenden\Bcs\Soap\PostfilialeType
     */
    public function setOrigin($Origin)
    {
      $this->Origin = $Origin;
      return $this;
    }

}
