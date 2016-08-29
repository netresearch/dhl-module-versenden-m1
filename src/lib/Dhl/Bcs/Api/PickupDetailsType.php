<?php

namespace Dhl\Bcs\Api;

class PickupDetailsType
{

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
     * @param PickupDate $PickupDate
     * @param ReadyByTime $ReadyByTime
     * @param ClosingTime $ClosingTime
     * @param string $PickupLocation
     */
    public function __construct($PickupDate, $ReadyByTime, $ClosingTime, $PickupLocation)
    {
      $this->PickupDate = $PickupDate;
      $this->ReadyByTime = $ReadyByTime;
      $this->ClosingTime = $ClosingTime;
      $this->PickupLocation = $PickupLocation;
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
     * @return \Dhl\Bcs\Api\PickupDetailsType
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
     * @return \Dhl\Bcs\Api\PickupDetailsType
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
     * @return \Dhl\Bcs\Api\PickupDetailsType
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
     * @return \Dhl\Bcs\Api\PickupDetailsType
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
     * @return \Dhl\Bcs\Api\PickupDetailsType
     */
    public function setPickupLocation($PickupLocation)
    {
      $this->PickupLocation = $PickupLocation;
      return $this;
    }

}
