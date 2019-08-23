<?php

namespace Dhl\Versenden\Bcs\Soap;

class ServiceconfigurationISR
{

    /**
     * @var anonymous167 $active
     */
    protected $active = null;

    /**
     * @var anonymous168 $details
     */
    protected $details = null;

    /**
     * @param anonymous167 $active
     * @param anonymous168 $details
     */
    public function __construct($active, $details)
    {
      $this->active = $active;
      $this->details = $details;
    }

    /**
     * @return anonymous167
     */
    public function getActive()
    {
      return $this->active;
    }

    /**
     * @param anonymous167 $active
     * @return \Dhl\Versenden\Bcs\Soap\ServiceconfigurationISR
     */
    public function setActive($active)
    {
      $this->active = $active;
      return $this;
    }

    /**
     * @return anonymous168
     */
    public function getDetails()
    {
      return $this->details;
    }

    /**
     * @param anonymous168 $details
     * @return \Dhl\Versenden\Bcs\Soap\ServiceconfigurationISR
     */
    public function setDetails($details)
    {
      $this->details = $details;
      return $this;
    }

}
