<?php

namespace Dhl\Versenden\Bcs\Soap;

class ServiceconfigurationDetailsOptional
{

    /**
     * @var anonymous156 $active
     */
    protected $active = null;

    /**
     * @var anonymous157 $details
     */
    protected $details = null;

    /**
     * @param anonymous156 $active
     * @param anonymous157 $details
     */
    public function __construct($active, $details)
    {
      $this->active = $active;
      $this->details = $details;
    }

    /**
     * @return anonymous156
     */
    public function getActive()
    {
      return $this->active;
    }

    /**
     * @param anonymous156 $active
     * @return \Dhl\Versenden\Bcs\Soap\ServiceconfigurationDetailsOptional
     */
    public function setActive($active)
    {
      $this->active = $active;
      return $this;
    }

    /**
     * @return anonymous157
     */
    public function getDetails()
    {
      return $this->details;
    }

    /**
     * @param anonymous157 $details
     * @return \Dhl\Versenden\Bcs\Soap\ServiceconfigurationDetailsOptional
     */
    public function setDetails($details)
    {
      $this->details = $details;
      return $this;
    }

}
