<?php

namespace Dhl\Bcs\Api;

class IdentityData
{

    /**
     * @var DrivingLicense $DrivingLicense
     */
    protected $DrivingLicense = null;

    /**
     * @var IdentityCard $IdentityCard
     */
    protected $IdentityCard = null;

    /**
     * @var BankCard $BankCard
     */
    protected $BankCard = null;

    /**
     * @param DrivingLicense $DrivingLicense
     * @param IdentityCard $IdentityCard
     * @param BankCard $BankCard
     */
    public function __construct($DrivingLicense, $IdentityCard, $BankCard)
    {
      $this->DrivingLicense = $DrivingLicense;
      $this->IdentityCard = $IdentityCard;
      $this->BankCard = $BankCard;
    }

    /**
     * @return DrivingLicense
     */
    public function getDrivingLicense()
    {
      return $this->DrivingLicense;
    }

    /**
     * @param DrivingLicense $DrivingLicense
     * @return \Dhl\Bcs\Api\IdentityData
     */
    public function setDrivingLicense($DrivingLicense)
    {
      $this->DrivingLicense = $DrivingLicense;
      return $this;
    }

    /**
     * @return IdentityCard
     */
    public function getIdentityCard()
    {
      return $this->IdentityCard;
    }

    /**
     * @param IdentityCard $IdentityCard
     * @return \Dhl\Bcs\Api\IdentityData
     */
    public function setIdentityCard($IdentityCard)
    {
      $this->IdentityCard = $IdentityCard;
      return $this;
    }

    /**
     * @return BankCard
     */
    public function getBankCard()
    {
      return $this->BankCard;
    }

    /**
     * @param BankCard $BankCard
     * @return \Dhl\Bcs\Api\IdentityData
     */
    public function setBankCard($BankCard)
    {
      $this->BankCard = $BankCard;
      return $this;
    }

}
