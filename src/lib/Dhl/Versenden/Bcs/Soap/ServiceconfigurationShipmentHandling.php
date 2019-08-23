<?php

namespace Dhl\Versenden\Bcs\Soap;

class ServiceconfigurationShipmentHandling
{

    /**
     * @var anonymous189 $active
     */
    protected $active = null;

    /**
     * @var anonymous190 $type
     */
    protected $type = null;

    /**
     * @param anonymous189 $active
     * @param anonymous190 $type
     */
    public function __construct($active, $type)
    {
      $this->active = $active;
      $this->type = $type;
    }

    /**
     * @return anonymous189
     */
    public function getActive()
    {
      return $this->active;
    }

    /**
     * @param anonymous189 $active
     * @return \Dhl\Versenden\Bcs\Soap\ServiceconfigurationShipmentHandling
     */
    public function setActive($active)
    {
      $this->active = $active;
      return $this;
    }

    /**
     * @return anonymous190
     */
    public function getType()
    {
      return $this->type;
    }

    /**
     * @param anonymous190 $type
     * @return \Dhl\Versenden\Bcs\Soap\ServiceconfigurationShipmentHandling
     */
    public function setType($type)
    {
      $this->type = $type;
      return $this;
    }

}
