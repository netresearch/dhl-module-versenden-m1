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
 * @package   Dhl\Versenden
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
namespace Dhl\Versenden\Config\Shipper;
use Dhl\Versenden\Config as ConfigReader;
use Dhl\Versenden\Config\Data as ConfigData;
use Dhl\Versenden\Config\Exception as ConfigException;
/**
 * BankData
 *
 * @deprecated
 * @see \Dhl\Versenden\Webservice\RequestData\ShipmentOrder\Shipper\BankData
 * @category Dhl
 * @package  Dhl\Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class BankData extends ConfigData
{
    /** @var string */
    public $accountOwner;
    /** @var string */
    public $bankName;
    /** @var string */
    public $iban;
    /** @var string */
    public $bic;
    /** @var string */
    public $note1;
    /** @var string */
    public $note2;
    /** @var string */
    public $accountReference;

    /**
     * Shift data from config array to properties.
     *
     * @param ConfigReader $reader
     */
    public function loadValues(ConfigReader $reader)
    {
        $this->accountOwner     = $reader->getValue('bankdata_owner');
        $this->bankName         = $reader->getValue('bankdata_bankname');
        $this->iban             = $reader->getValue('bankdata_iban');
        $this->bic              = $reader->getValue('bankdata_bic');
        $this->note1            = $reader->getValue('bankdata_note1');
        $this->note2            = $reader->getValue('bankdata_note2');
        $this->accountReference = $reader->getValue('bankdata_accountreference');
    }

    /**
     * Validate values in addition to system.xml frontend validation
     *
     * @param ConfigReader $reader
     * @throws ConfigException
     */
    public function validateValues(ConfigReader $reader)
    {
        $reader->validateLength('Account Owner', $this->accountOwner, 1, 80);
        $reader->validateLength('Bank Name', $this->accountOwner, 1, 80);
        $reader->validateLength('IBAN', $this->iban, 1, 34);
        $reader->validateLength('BIC', $this->bic, 0, 11);
        $reader->validateLength('Note1', $this->note1, 0, 35);
        $reader->validateLength('Note2', $this->note2, 0, 35);
        $reader->validateLength('Account Reference', $this->accountReference, 0, 35);
    }
}
