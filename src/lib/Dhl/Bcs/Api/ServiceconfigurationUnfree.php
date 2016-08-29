<?php

namespace Dhl\Bcs\Api;

class ServiceconfigurationUnfree
{

    /**
     * @var anonymous161 $active
     */
    protected $active = null;

    /**
     * @var anonymous162 $PaymentType
     */
    protected $PaymentType = null;

    /**
     * @var anonymous163 $CustomerNumber
     */
    protected $CustomerNumber = null;

    /**
     * @param anonymous161 $active
     * @param anonymous162 $PaymentType
     * @param anonymous163 $CustomerNumber
     */
    public function __construct($active, $PaymentType, $CustomerNumber)
    {
      $this->active = $active;
      $this->PaymentType = $PaymentType;
      $this->CustomerNumber = $CustomerNumber;
    }

    /**
     * @return anonymous161
     */
    public function getActive()
    {
      return $this->active;
    }

    /**
     * @param anonymous161 $active
     * @return \Dhl\Bcs\Api\ServiceconfigurationUnfree
     */
    public function setActive($active)
    {
      $this->active = $active;
      return $this;
    }

    /**
     * @return anonymous162
     */
    public function getPaymentType()
    {
      return $this->PaymentType;
    }

    /**
     * @param anonymous162 $PaymentType
     * @return \Dhl\Bcs\Api\ServiceconfigurationUnfree
     */
    public function setPaymentType($PaymentType)
    {
      $this->PaymentType = $PaymentType;
      return $this;
    }

    /**
     * @return anonymous163
     */
    public function getCustomerNumber()
    {
      return $this->CustomerNumber;
    }

    /**
     * @param anonymous163 $CustomerNumber
     * @return \Dhl\Bcs\Api\ServiceconfigurationUnfree
     */
    public function setCustomerNumber($CustomerNumber)
    {
      $this->CustomerNumber = $CustomerNumber;
      return $this;
    }

}
