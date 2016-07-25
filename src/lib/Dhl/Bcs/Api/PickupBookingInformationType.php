<?php

namespace Dhl\Bcs\Api;

class PickupBookingInformationType
{

    /**
     * @var Account $Account
     */
    protected $Account = null;

    /**
     * @var PickupDate $PickupDate
     */
    protected $PickupDate = null;

    /**
     * @var ReadyByTime $ReadyByTime
     */
    protected $ReadyByTime = null;

    /**
     * @var ClosingTime $ClosingTime
     */
    protected $ClosingTime = null;

    /**
     * @var string $Remark
     */
    protected $Remark = null;

    /**
     * @var string $PickupLocation
     */
    protected $PickupLocation = null;

    /**
     * @var AmountOfPieces $AmountOfPieces
     */
    protected $AmountOfPieces = null;

    /**
     * @var AmountOfPallets $AmountOfPallets
     */
    protected $AmountOfPallets = null;

    /**
     * @var WeightInKG $WeightInKG
     */
    protected $WeightInKG = null;

    /**
     * @var CountOfShipments $CountOfShipments
     */
    protected $CountOfShipments = null;

    /**
     * @var TotalVolumeWeight $TotalVolumeWeight
     */
    protected $TotalVolumeWeight = null;

    /**
     * @var MaxLengthInCM $MaxLengthInCM
     */
    protected $MaxLengthInCM = null;

    /**
     * @var MaxWidthInCM $MaxWidthInCM
     */
    protected $MaxWidthInCM = null;

    /**
     * @var MaxHeightInCM $MaxHeightInCM
     */
    protected $MaxHeightInCM = null;

    /**
     * @param Account $Account
     * @param PickupDate $PickupDate
     * @param ReadyByTime $ReadyByTime
     * @param ClosingTime $ClosingTime
     */
    public function __construct($Account, $PickupDate, $ReadyByTime, $ClosingTime)
    {
      $this->Account = $Account;
      $this->PickupDate = $PickupDate;
      $this->ReadyByTime = $ReadyByTime;
      $this->ClosingTime = $ClosingTime;
    }

    /**
     * @return Account
     */
    public function getAccount()
    {
      return $this->Account;
    }

    /**
     * @param Account $Account
     * @return \Dhl\Bcs\Api\PickupBookingInformationType
     */
    public function setAccount($Account)
    {
      $this->Account = $Account;
      return $this;
    }

    /**
     * @return PickupDate
     */
    public function getPickupDate()
    {
      return $this->PickupDate;
    }

    /**
     * @param PickupDate $PickupDate
     * @return \Dhl\Bcs\Api\PickupBookingInformationType
     */
    public function setPickupDate($PickupDate)
    {
      $this->PickupDate = $PickupDate;
      return $this;
    }

    /**
     * @return ReadyByTime
     */
    public function getReadyByTime()
    {
      return $this->ReadyByTime;
    }

    /**
     * @param ReadyByTime $ReadyByTime
     * @return \Dhl\Bcs\Api\PickupBookingInformationType
     */
    public function setReadyByTime($ReadyByTime)
    {
      $this->ReadyByTime = $ReadyByTime;
      return $this;
    }

    /**
     * @return ClosingTime
     */
    public function getClosingTime()
    {
      return $this->ClosingTime;
    }

    /**
     * @param ClosingTime $ClosingTime
     * @return \Dhl\Bcs\Api\PickupBookingInformationType
     */
    public function setClosingTime($ClosingTime)
    {
      $this->ClosingTime = $ClosingTime;
      return $this;
    }

    /**
     * @return string
     */
    public function getRemark()
    {
      return $this->Remark;
    }

    /**
     * @param string $Remark
     * @return \Dhl\Bcs\Api\PickupBookingInformationType
     */
    public function setRemark($Remark)
    {
      $this->Remark = $Remark;
      return $this;
    }

    /**
     * @return string
     */
    public function getPickupLocation()
    {
      return $this->PickupLocation;
    }

    /**
     * @param string $PickupLocation
     * @return \Dhl\Bcs\Api\PickupBookingInformationType
     */
    public function setPickupLocation($PickupLocation)
    {
      $this->PickupLocation = $PickupLocation;
      return $this;
    }

    /**
     * @return AmountOfPieces
     */
    public function getAmountOfPieces()
    {
      return $this->AmountOfPieces;
    }

    /**
     * @param AmountOfPieces $AmountOfPieces
     * @return \Dhl\Bcs\Api\PickupBookingInformationType
     */
    public function setAmountOfPieces($AmountOfPieces)
    {
      $this->AmountOfPieces = $AmountOfPieces;
      return $this;
    }

    /**
     * @return AmountOfPallets
     */
    public function getAmountOfPallets()
    {
      return $this->AmountOfPallets;
    }

    /**
     * @param AmountOfPallets $AmountOfPallets
     * @return \Dhl\Bcs\Api\PickupBookingInformationType
     */
    public function setAmountOfPallets($AmountOfPallets)
    {
      $this->AmountOfPallets = $AmountOfPallets;
      return $this;
    }

    /**
     * @return WeightInKG
     */
    public function getWeightInKG()
    {
      return $this->WeightInKG;
    }

    /**
     * @param WeightInKG $WeightInKG
     * @return \Dhl\Bcs\Api\PickupBookingInformationType
     */
    public function setWeightInKG($WeightInKG)
    {
      $this->WeightInKG = $WeightInKG;
      return $this;
    }

    /**
     * @return CountOfShipments
     */
    public function getCountOfShipments()
    {
      return $this->CountOfShipments;
    }

    /**
     * @param CountOfShipments $CountOfShipments
     * @return \Dhl\Bcs\Api\PickupBookingInformationType
     */
    public function setCountOfShipments($CountOfShipments)
    {
      $this->CountOfShipments = $CountOfShipments;
      return $this;
    }

    /**
     * @return TotalVolumeWeight
     */
    public function getTotalVolumeWeight()
    {
      return $this->TotalVolumeWeight;
    }

    /**
     * @param TotalVolumeWeight $TotalVolumeWeight
     * @return \Dhl\Bcs\Api\PickupBookingInformationType
     */
    public function setTotalVolumeWeight($TotalVolumeWeight)
    {
      $this->TotalVolumeWeight = $TotalVolumeWeight;
      return $this;
    }

    /**
     * @return MaxLengthInCM
     */
    public function getMaxLengthInCM()
    {
      return $this->MaxLengthInCM;
    }

    /**
     * @param MaxLengthInCM $MaxLengthInCM
     * @return \Dhl\Bcs\Api\PickupBookingInformationType
     */
    public function setMaxLengthInCM($MaxLengthInCM)
    {
      $this->MaxLengthInCM = $MaxLengthInCM;
      return $this;
    }

    /**
     * @return MaxWidthInCM
     */
    public function getMaxWidthInCM()
    {
      return $this->MaxWidthInCM;
    }

    /**
     * @param MaxWidthInCM $MaxWidthInCM
     * @return \Dhl\Bcs\Api\PickupBookingInformationType
     */
    public function setMaxWidthInCM($MaxWidthInCM)
    {
      $this->MaxWidthInCM = $MaxWidthInCM;
      return $this;
    }

    /**
     * @return MaxHeightInCM
     */
    public function getMaxHeightInCM()
    {
      return $this->MaxHeightInCM;
    }

    /**
     * @param MaxHeightInCM $MaxHeightInCM
     * @return \Dhl\Bcs\Api\PickupBookingInformationType
     */
    public function setMaxHeightInCM($MaxHeightInCM)
    {
      $this->MaxHeightInCM = $MaxHeightInCM;
      return $this;
    }

}
