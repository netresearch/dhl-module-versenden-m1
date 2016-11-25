<?php

namespace Dhl\Versenden\Bcs\Soap;

class ServiceconfigurationVisualAgeCheck
{

    /**
     * @var anonymous142 $active
     */
    protected $active = null;

    /**
     * @var anonymous143 $type
     */
    protected $type = null;

    /**
     * @param anonymous142 $active
     * @param anonymous143 $type
     */
    public function __construct($active, $type)
    {
      $this->active = $active;
      $this->type = $type;
    }

    /**
     * @return anonymous142
     */
    public function getActive()
    {
      return $this->active;
    }

    /**
     * @param anonymous142 $active
     * @return \Dhl\Versenden\Bcs\Soap\ServiceconfigurationVisualAgeCheck
     */
    public function setActive($active)
    {
      $this->active = $active;
      return $this;
    }

    /**
     * @return anonymous143
     */
    public function getType()
    {
      return $this->type;
    }

    /**
     * @param anonymous143 $type
     * @return \Dhl\Versenden\Bcs\Soap\ServiceconfigurationVisualAgeCheck
     */
    public function setType($type)
    {
      $this->type = $type;
      return $this;
    }

}
