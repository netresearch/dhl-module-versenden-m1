<?php

namespace Dhl\Bcs\Api;

class DrivingLicense
{

    /**
     * @var string $LicenseNumber
     */
    protected $LicenseNumber = null;

    /**
     * @var string $Authority
     */
    protected $Authority = null;

    /**
     * @param string $LicenseNumber
     * @param string $Authority
     */
    public function __construct($LicenseNumber, $Authority)
    {
      $this->LicenseNumber = $LicenseNumber;
      $this->Authority = $Authority;
    }

    /**
     * @return string
     */
    public function getLicenseNumber()
    {
      return $this->LicenseNumber;
    }

    /**
     * @param string $LicenseNumber
     * @return \Dhl\Bcs\Api\DrivingLicense
     */
    public function setLicenseNumber($LicenseNumber)
    {
      $this->LicenseNumber = $LicenseNumber;
      return $this;
    }

    /**
     * @return string
     */
    public function getAuthority()
    {
      return $this->Authority;
    }

    /**
     * @param string $Authority
     * @return \Dhl\Bcs\Api\DrivingLicense
     */
    public function setAuthority($Authority)
    {
      $this->Authority = $Authority;
      return $this;
    }

}
