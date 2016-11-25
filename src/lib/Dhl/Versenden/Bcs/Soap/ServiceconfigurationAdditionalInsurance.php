<?php

namespace Dhl\Versenden\Bcs\Soap;

class ServiceconfigurationAdditionalInsurance
{

    /**
     * @var anonymous151 $active
     */
    protected $active = null;

    /**
     * @var anonymous152 $insuranceAmount
     */
    protected $insuranceAmount = null;

    /**
     * @param anonymous151 $active
     * @param anonymous152 $insuranceAmount
     */
    public function __construct($active, $insuranceAmount)
    {
      $this->active = $active;
      $this->insuranceAmount = $insuranceAmount;
    }

    /**
     * @return anonymous151
     */
    public function getActive()
    {
      return $this->active;
    }

    /**
     * @param anonymous151 $active
     * @return \Dhl\Versenden\Bcs\Soap\ServiceconfigurationAdditionalInsurance
     */
    public function setActive($active)
    {
      $this->active = $active;
      return $this;
    }

    /**
     * @return anonymous152
     */
    public function getInsuranceAmount()
    {
      return $this->insuranceAmount;
    }

    /**
     * @param anonymous152 $insuranceAmount
     * @return \Dhl\Versenden\Bcs\Soap\ServiceconfigurationAdditionalInsurance
     */
    public function setInsuranceAmount($insuranceAmount)
    {
      $this->insuranceAmount = $insuranceAmount;
      return $this;
    }

}
