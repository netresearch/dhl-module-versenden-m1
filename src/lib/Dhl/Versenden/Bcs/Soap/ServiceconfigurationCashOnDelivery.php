<?php

namespace Dhl\Versenden\Bcs\Soap;

class ServiceconfigurationCashOnDelivery
{

    /**
     * @var anonymous185 $active
     */
    protected $active = null;

    /**
     * @var anonymous186 $addFee
     */
    protected $addFee = null;

    /**
     * @var anonymous187 $codAmount
     */
    protected $codAmount = null;

    /**
     * @param anonymous185 $active
     * @param anonymous186 $addFee
     * @param anonymous187 $codAmount
     */
    public function __construct($active, $addFee, $codAmount)
    {
      $this->active = $active;
      $this->addFee = $addFee;
      $this->codAmount = $codAmount;
    }

    /**
     * @return anonymous185
     */
    public function getActive()
    {
      return $this->active;
    }

    /**
     * @param anonymous185 $active
     * @return \Dhl\Versenden\Bcs\Soap\ServiceconfigurationCashOnDelivery
     */
    public function setActive($active)
    {
      $this->active = $active;
      return $this;
    }

    /**
     * @return anonymous186
     */
    public function getAddFee()
    {
      return $this->addFee;
    }

    /**
     * @param anonymous186 $addFee
     * @return \Dhl\Versenden\Bcs\Soap\ServiceconfigurationCashOnDelivery
     */
    public function setAddFee($addFee)
    {
      $this->addFee = $addFee;
      return $this;
    }

    /**
     * @return anonymous187
     */
    public function getCodAmount()
    {
      return $this->codAmount;
    }

    /**
     * @param anonymous187 $codAmount
     * @return \Dhl\Versenden\Bcs\Soap\ServiceconfigurationCashOnDelivery
     */
    public function setCodAmount($codAmount)
    {
      $this->codAmount = $codAmount;
      return $this;
    }

}
