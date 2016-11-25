<?php

namespace Dhl\Versenden\Bcs\Soap;

class ServiceconfigurationCashOnDelivery
{

    /**
     * @var anonymous154 $active
     */
    protected $active = null;

    /**
     * @var anonymous155 $addFee
     */
    protected $addFee = null;

    /**
     * @var anonymous156 $codAmount
     */
    protected $codAmount = null;

    /**
     * @param anonymous154 $active
     * @param anonymous155 $addFee
     * @param anonymous156 $codAmount
     */
    public function __construct($active, $addFee, $codAmount)
    {
      $this->active = $active;
      $this->addFee = $addFee;
      $this->codAmount = $codAmount;
    }

    /**
     * @return anonymous154
     */
    public function getActive()
    {
      return $this->active;
    }

    /**
     * @param anonymous154 $active
     * @return \Dhl\Versenden\Bcs\Soap\ServiceconfigurationCashOnDelivery
     */
    public function setActive($active)
    {
      $this->active = $active;
      return $this;
    }

    /**
     * @return anonymous155
     */
    public function getAddFee()
    {
      return $this->addFee;
    }

    /**
     * @param anonymous155 $addFee
     * @return \Dhl\Versenden\Bcs\Soap\ServiceconfigurationCashOnDelivery
     */
    public function setAddFee($addFee)
    {
      $this->addFee = $addFee;
      return $this;
    }

    /**
     * @return anonymous156
     */
    public function getCodAmount()
    {
      return $this->codAmount;
    }

    /**
     * @param anonymous156 $codAmount
     * @return \Dhl\Versenden\Bcs\Soap\ServiceconfigurationCashOnDelivery
     */
    public function setCodAmount($codAmount)
    {
      $this->codAmount = $codAmount;
      return $this;
    }

}
