<?php

namespace Dhl\Bcs\Api;

class CustomerType
{

    /**
     * @var NameType $Name
     */
    protected $Name = null;

    /**
     * @var string $vatID
     */
    protected $vatID = null;

    /**
     * @var EKP $EKP
     */
    protected $EKP = null;

    /**
     * @var NativeAddressType $Address
     */
    protected $Address = null;

    /**
     * @var ContactType $Contact
     */
    protected $Contact = null;

    /**
     * @var BankType $Bank
     */
    protected $Bank = null;

    /**
     * @var anyType $note
     */
    protected $note = null;

    /**
     * @param NameType $Name
     * @param EKP $EKP
     * @param NativeAddressType $Address
     * @param ContactType $Contact
     */
    public function __construct($Name, $EKP, $Address, $Contact)
    {
      $this->Name = $Name;
      $this->EKP = $EKP;
      $this->Address = $Address;
      $this->Contact = $Contact;
    }

    /**
     * @return NameType
     */
    public function getName()
    {
      return $this->Name;
    }

    /**
     * @param NameType $Name
     * @return \Dhl\Bcs\Api\CustomerType
     */
    public function setName($Name)
    {
      $this->Name = $Name;
      return $this;
    }

    /**
     * @return string
     */
    public function getVatID()
    {
      return $this->vatID;
    }

    /**
     * @param string $vatID
     * @return \Dhl\Bcs\Api\CustomerType
     */
    public function setVatID($vatID)
    {
      $this->vatID = $vatID;
      return $this;
    }

    /**
     * @return EKP
     */
    public function getEKP()
    {
      return $this->EKP;
    }

    /**
     * @param EKP $EKP
     * @return \Dhl\Bcs\Api\CustomerType
     */
    public function setEKP($EKP)
    {
      $this->EKP = $EKP;
      return $this;
    }

    /**
     * @return NativeAddressType
     */
    public function getAddress()
    {
      return $this->Address;
    }

    /**
     * @param NativeAddressType $Address
     * @return \Dhl\Bcs\Api\CustomerType
     */
    public function setAddress($Address)
    {
      $this->Address = $Address;
      return $this;
    }

    /**
     * @return ContactType
     */
    public function getContact()
    {
      return $this->Contact;
    }

    /**
     * @param ContactType $Contact
     * @return \Dhl\Bcs\Api\CustomerType
     */
    public function setContact($Contact)
    {
      $this->Contact = $Contact;
      return $this;
    }

    /**
     * @return BankType
     */
    public function getBank()
    {
      return $this->Bank;
    }

    /**
     * @param BankType $Bank
     * @return \Dhl\Bcs\Api\CustomerType
     */
    public function setBank($Bank)
    {
      $this->Bank = $Bank;
      return $this;
    }

    /**
     * @return anyType
     */
    public function getNote()
    {
      return $this->note;
    }

    /**
     * @param anyType $note
     * @return \Dhl\Bcs\Api\CustomerType
     */
    public function setNote($note)
    {
      $this->note = $note;
      return $this;
    }

}
