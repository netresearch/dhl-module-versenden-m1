<?php

namespace Dhl\Versenden\Bcs\Soap;

class ServiceconfigurationEndorsement
{

    /**
     * @var anonymous164 $active
     */
    protected $active = null;

    /**
     * @var anonymous165 $type
     */
    protected $type = null;

    /**
     * @param anonymous164 $active
     * @param anonymous165 $type
     */
    public function __construct($active, $type)
    {
      $this->active = $active;
      $this->type = $type;
    }

    /**
     * @return anonymous164
     */
    public function getActive()
    {
      return $this->active;
    }

    /**
     * @param anonymous164 $active
     * @return \Dhl\Versenden\Bcs\Soap\ServiceconfigurationEndorsement
     */
    public function setActive($active)
    {
      $this->active = $active;
      return $this;
    }

    /**
     * @return anonymous165
     */
    public function getType()
    {
      return $this->type;
    }

    /**
     * @param anonymous165 $type
     * @return \Dhl\Versenden\Bcs\Soap\ServiceconfigurationEndorsement
     */
    public function setType($type)
    {
      $this->type = $type;
      return $this;
    }

}
