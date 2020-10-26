<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder\Shipper;

use Dhl\Versenden\Bcs\Api\Webservice\RequestData;

class BankData extends RequestData
{
    /** @var string */
    private $accountOwner;
    /** @var string */
    private $bankName;
    /** @var string */
    private $iban;
    /** @var string */
    private $bic;
    /** @var string */
    private $note1;
    /** @var string */
    private $note2;
    /** @var string */
    private $accountReference;

    /**
     * BankData constructor.
     * @param string $accountOwner
     * @param string $bankName
     * @param string $iban
     * @param string $bic
     * @param string $note1
     * @param string $note2
     * @param string $accountReference
     */
    public function __construct($accountOwner, $bankName, $iban, $bic, $note1, $note2, $accountReference)
    {
        $this->validateLength('Account Owner', $accountOwner, 1, 80);
        $this->validateLength('Bank Name', $bankName, 1, 80);
        $this->validateLength('IBAN', $iban, 1, 34);
        $this->validateLength('BIC', $bic, 0, 11);
        $this->validateLength('Note1', $note1, 0, 35);
        $this->validateLength('Note2', $note2, 0, 35);
        $this->validateLength('Account Reference', $accountReference, 0, 35);

        $this->accountOwner = $accountOwner;
        $this->bankName = $bankName;
        $this->iban = $iban;
        $this->bic = $bic;
        $this->note1 = $note1;
        $this->note2 = $note2;
        $this->accountReference = $accountReference;
    }

    /**
     * @return string
     */
    public function getAccountOwner()
    {
        return $this->accountOwner;
    }

    /**
     * @return string
     */
    public function getBankName()
    {
        return $this->bankName;
    }

    /**
     * @return string
     */
    public function getIban()
    {
        return $this->iban;
    }

    /**
     * @return string
     */
    public function getBic()
    {
        return $this->bic;
    }

    /**
     * @return string
     */
    public function getNote1()
    {
        return $this->note1;
    }

    /**
     * @return string
     */
    public function getNote2()
    {
        return $this->note2;
    }

    /**
     * @return string
     */
    public function getAccountReference()
    {
        return $this->accountReference;
    }
}
