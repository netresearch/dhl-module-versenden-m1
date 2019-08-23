<?php

namespace Dhl\Versenden\Bcs\Soap;

class ServiceconfigurationVisualAgeCheck
{

    /**
     * @var anonymous173 $active
     */
    protected $active = null;

    /**
     * @var anonymous174 $type
     */
    protected $type = null;

    /**
     * @param anonymous173 $active
     * @param anonymous174 $type
     */
    public function __construct($active, $type)
    {
      $this->active = $active;
      $this->type = $type;
    }

    /**
     * @return anonymous173
     */
    public function getActive()
    {
      return $this->active;
    }

    /**
     * @param anonymous173 $active
     * @return \Dhl\Versenden\Bcs\Soap\ServiceconfigurationVisualAgeCheck
     */
    public function setActive($active)
    {
      $this->active = $active;
      return $this;
    }

    /**
     * @return anonymous174
     */
    public function getType()
    {
      return $this->type;
    }

    /**
     * @param anonymous174 $type
     * @return \Dhl\Versenden\Bcs\Soap\ServiceconfigurationVisualAgeCheck
     */
    public function setType($type)
    {
      $this->type = $type;
      return $this;
    }

}
