<?php

namespace Dhl\Versenden\Bcs\Soap;

class ShipmentNotificationType
{

    /**
     * @var recipientEmailAddress $recipientEmailAddress
     */
    protected $recipientEmailAddress = null;

    /**
     * @param recipientEmailAddress $recipientEmailAddress
     */
    public function __construct($recipientEmailAddress)
    {
      $this->recipientEmailAddress = $recipientEmailAddress;
    }

    /**
     * @return recipientEmailAddress
     */
    public function getRecipientEmailAddress()
    {
      return $this->recipientEmailAddress;
    }

    /**
     * @param recipientEmailAddress $recipientEmailAddress
     * @return \Dhl\Versenden\Bcs\Soap\ShipmentNotificationType
     */
    public function setRecipientEmailAddress($recipientEmailAddress)
    {
      $this->recipientEmailAddress = $recipientEmailAddress;
      return $this;
    }

}
