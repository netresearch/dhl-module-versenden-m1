<?php

namespace Dhl\Bcs\Api;

class ServiceconfigurationType
{

    /**
     * @var anonymous130 $active
     */
    protected $active = null;

    /**
     * @var anonymous131 $Type
     */
    protected $Type = null;

    /**
     * @param anonymous130 $active
     * @param anonymous131 $Type
     */
    public function __construct($active, $Type)
    {
      $this->active = $active;
      $this->Type = $Type;
    }

    /**
     * @return anonymous130
     */
    public function getActive()
    {
      return $this->active;
    }

    /**
     * @param anonymous130 $active
     * @return \Dhl\Bcs\Api\ServiceconfigurationType
     */
    public function setActive($active)
    {
      $this->active = $active;
      return $this;
    }

    /**
     * @return anonymous131
     */
    public function getType()
    {
      return $this->Type;
    }

    /**
     * @param anonymous131 $Type
     * @return \Dhl\Bcs\Api\ServiceconfigurationType
     */
    public function setType($Type)
    {
      $this->Type = $Type;
      return $this;
    }

}
