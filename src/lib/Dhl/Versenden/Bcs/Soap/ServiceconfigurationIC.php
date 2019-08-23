<?php

namespace Dhl\Versenden\Bcs\Soap;

class ServiceconfigurationIC
{

    /**
     * @var Ident $Ident
     */
    protected $Ident = null;

    /**
     * @var anonymous201 $active
     */
    protected $active = null;

    /**
     * @param Ident $Ident
     * @param anonymous201 $active
     */
    public function __construct($Ident, $active)
    {
      $this->Ident = $Ident;
      $this->active = $active;
    }

    /**
     * @return Ident
     */
    public function getIdent()
    {
      return $this->Ident;
    }

    /**
     * @param Ident $Ident
     * @return \Dhl\Versenden\Bcs\Soap\ServiceconfigurationIC
     */
    public function setIdent($Ident)
    {
      $this->Ident = $Ident;
      return $this;
    }

    /**
     * @return anonymous201
     */
    public function getActive()
    {
      return $this->active;
    }

    /**
     * @param anonymous201 $active
     * @return \Dhl\Versenden\Bcs\Soap\ServiceconfigurationIC
     */
    public function setActive($active)
    {
      $this->active = $active;
      return $this;
    }

}
