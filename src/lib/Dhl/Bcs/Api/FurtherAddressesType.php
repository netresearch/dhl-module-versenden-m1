<?php

namespace Dhl\Bcs\Api;

class FurtherAddressesType
{

    /**
     * @var DeliveryAdress $DeliveryAdress
     */
    protected $DeliveryAdress = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return DeliveryAdress
     */
    public function getDeliveryAdress()
    {
      return $this->DeliveryAdress;
    }

    /**
     * @param DeliveryAdress $DeliveryAdress
     * @return \Dhl\Bcs\Api\FurtherAddressesType
     */
    public function setDeliveryAdress($DeliveryAdress)
    {
      $this->DeliveryAdress = $DeliveryAdress;
      return $this;
    }

}
