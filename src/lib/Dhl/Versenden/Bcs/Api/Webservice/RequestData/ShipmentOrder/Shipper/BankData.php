<?php
/**
 * Dhl Versenden
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer versions in the future.
 *
 * PHP version 5
 *
 * @category  Dhl
 * @package   Dhl\Versenden\Bcs\Api\Webservice\RequestData
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
namespace Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder\Shipper;
use Dhl\Versenden\Bcs\Api\Webservice\RequestData;

/**
 * BankData
 *
 * @category Dhl
 * @package  Dhl\Versenden\Bcs\Api\Webservice\RequestData
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
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
