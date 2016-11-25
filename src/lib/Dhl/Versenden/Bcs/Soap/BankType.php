<?php

namespace Dhl\Versenden\Bcs\Soap;

class BankType
{

    /**
     * @var accountOwner $accountOwner
     */
    protected $accountOwner = null;

    /**
     * @var bankName $bankName
     */
    protected $bankName = null;

    /**
     * @var iban $iban
     */
    protected $iban = null;

    /**
     * @var note1 $note1
     */
    protected $note1 = null;

    /**
     * @var note2 $note2
     */
    protected $note2 = null;

    /**
     * @var bic $bic
     */
    protected $bic = null;

    /**
     * @var accountreference $accountreference
     */
    protected $accountreference = null;

    /**
     * @param accountOwner $accountOwner
     * @param bankName $bankName
     * @param iban $iban
     */
    public function __construct($accountOwner, $bankName, $iban)
    {
      $this->accountOwner = $accountOwner;
      $this->bankName = $bankName;
      $this->iban = $iban;
    }

    /**
     * @return accountOwner
     */
    public function getAccountOwner()
    {
      return $this->accountOwner;
    }

    /**
     * @param accountOwner $accountOwner
     * @return \Dhl\Versenden\Bcs\Soap\BankType
     */
    public function setAccountOwner($accountOwner)
    {
      $this->accountOwner = $accountOwner;
      return $this;
    }

    /**
     * @return bankName
     */
    public function getBankName()
    {
      return $this->bankName;
    }

    /**
     * @param bankName $bankName
     * @return \Dhl\Versenden\Bcs\Soap\BankType
     */
    public function setBankName($bankName)
    {
      $this->bankName = $bankName;
      return $this;
    }

    /**
     * @return iban
     */
    public function getIban()
    {
      return $this->iban;
    }

    /**
     * @param iban $iban
     * @return \Dhl\Versenden\Bcs\Soap\BankType
     */
    public function setIban($iban)
    {
      $this->iban = $iban;
      return $this;
    }

    /**
     * @return note1
     */
    public function getNote1()
    {
      return $this->note1;
    }

    /**
     * @param note1 $note1
     * @return \Dhl\Versenden\Bcs\Soap\BankType
     */
    public function setNote1($note1)
    {
      $this->note1 = $note1;
      return $this;
    }

    /**
     * @return note2
     */
    public function getNote2()
    {
      return $this->note2;
    }

    /**
     * @param note2 $note2
     * @return \Dhl\Versenden\Bcs\Soap\BankType
     */
    public function setNote2($note2)
    {
      $this->note2 = $note2;
      return $this;
    }

    /**
     * @return bic
     */
    public function getBic()
    {
      return $this->bic;
    }

    /**
     * @param bic $bic
     * @return \Dhl\Versenden\Bcs\Soap\BankType
     */
    public function setBic($bic)
    {
      $this->bic = $bic;
      return $this;
    }

    /**
     * @return accountreference
     */
    public function getAccountreference()
    {
      return $this->accountreference;
    }

    /**
     * @param accountreference $accountreference
     * @return \Dhl\Versenden\Bcs\Soap\BankType
     */
    public function setAccountreference($accountreference)
    {
      $this->accountreference = $accountreference;
      return $this;
    }

}
