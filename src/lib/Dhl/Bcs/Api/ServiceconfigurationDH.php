<?php

namespace Dhl\Bcs\Api;

class ServiceconfigurationDH
{

    /**
     * @var anonymous139 $active
     */
    protected $active = null;

    /**
     * @var anonymous140 $Days
     */
    protected $Days = null;

    /**
     * @param anonymous139 $active
     * @param anonymous140 $Days
     */
    public function __construct($active, $Days)
    {
      $this->active = $active;
      $this->Days = $Days;
    }

    /**
     * @return anonymous139
     */
    public function getActive()
    {
      return $this->active;
    }

    /**
     * @param anonymous139 $active
     * @return \Dhl\Bcs\Api\ServiceconfigurationDH
     */
    public function setActive($active)
    {
      $this->active = $active;
      return $this;
    }

    /**
     * @return anonymous140
     */
    public function getDays()
    {
      return $this->Days;
    }

    /**
     * @param anonymous140 $Days
     * @return \Dhl\Bcs\Api\ServiceconfigurationDH
     */
    public function setDays($Days)
    {
      $this->Days = $Days;
      return $this;
    }

}
