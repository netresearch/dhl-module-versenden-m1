<?php

namespace Netresearch\Dhl\Bcs\Api;

class CommunicationType
{

    /**
     * @var phone $phone
     */
    protected $phone = null;

    /**
     * @var email $email
     */
    protected $email = null;

    /**
     * @var contactPerson $contactPerson
     */
    protected $contactPerson = null;


    public function __construct()
    {

    }

    /**
     * @return phone
     */
    public function getPhone()
    {
      return $this->phone;
    }

    /**
     * @param phone $phone
     * @return \Netresearch\Dhl\Bcs\Api\CommunicationType
     */
    public function setPhone($phone)
    {
      $this->phone = $phone;
      return $this;
    }

    /**
     * @return email
     */
    public function getEmail()
    {
      return $this->email;
    }

    /**
     * @param email $email
     * @return \Netresearch\Dhl\Bcs\Api\CommunicationType
     */
    public function setEmail($email)
    {
      $this->email = $email;
      return $this;
    }

    /**
     * @return contactPerson
     */
    public function getContactPerson()
    {
      return $this->contactPerson;
    }

    /**
     * @param contactPerson $contactPerson
     * @return \Netresearch\Dhl\Bcs\Api\CommunicationType
     */
    public function setContactPerson($contactPerson)
    {
      $this->contactPerson = $contactPerson;
      return $this;
    }

}
