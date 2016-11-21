<?php

namespace Netresearch\Dhl\Bcs\Api;

class ServiceconfigurationDeliveryTimeframe
{

    /**
     * @var anonymous145 $active
     */
    protected $active = null;

    /**
     * @var anonymous146 $type
     */
    protected $type = null;

    /**
     * @param anonymous145 $active
     * @param anonymous146 $type
     */
    public function __construct($active, $type)
    {
      $this->active = $active;
      $this->type = $type;
    }

    /**
     * @return anonymous145
     */
    public function getActive()
    {
      return $this->active;
    }

    /**
     * @param anonymous145 $active
     * @return \Netresearch\Dhl\Bcs\Api\ServiceconfigurationDeliveryTimeframe
     */
    public function setActive($active)
    {
      $this->active = $active;
      return $this;
    }

    /**
     * @return anonymous146
     */
    public function getType()
    {
      return $this->type;
    }

    /**
     * @param anonymous146 $type
     * @return \Netresearch\Dhl\Bcs\Api\ServiceconfigurationDeliveryTimeframe
     */
    public function setType($type)
    {
      $this->type = $type;
      return $this;
    }

}
