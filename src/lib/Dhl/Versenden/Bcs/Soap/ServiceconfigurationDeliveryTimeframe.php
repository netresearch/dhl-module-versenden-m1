<?php

namespace Dhl\Versenden\Bcs\Soap;

class ServiceconfigurationDeliveryTimeframe
{

    /**
     * @var anonymous176 $active
     */
    protected $active = null;

    /**
     * @var anonymous177 $type
     */
    protected $type = null;

    /**
     * @param anonymous176 $active
     * @param anonymous177 $type
     */
    public function __construct($active, $type)
    {
      $this->active = $active;
      $this->type = $type;
    }

    /**
     * @return anonymous176
     */
    public function getActive()
    {
      return $this->active;
    }

    /**
     * @param anonymous176 $active
     * @return \Dhl\Versenden\Bcs\Soap\ServiceconfigurationDeliveryTimeframe
     */
    public function setActive($active)
    {
      $this->active = $active;
      return $this;
    }

    /**
     * @return anonymous177
     */
    public function getType()
    {
      return $this->type;
    }

    /**
     * @param anonymous177 $type
     * @return \Dhl\Versenden\Bcs\Soap\ServiceconfigurationDeliveryTimeframe
     */
    public function setType($type)
    {
      $this->type = $type;
      return $this;
    }

}
