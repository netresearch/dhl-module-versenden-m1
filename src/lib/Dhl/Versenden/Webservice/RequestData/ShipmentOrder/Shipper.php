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
 * @package   Dhl\Versenden\Webservice\RequestData
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
namespace Dhl\Versenden\Webservice\RequestData\ShipmentOrder;
use Dhl\Versenden\Webservice\RequestData;

/**
 * Shipper
 *
 * @category Dhl
 * @package  Dhl\Versenden\Webservice\RequestData
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Shipper extends RequestData
{
    /** @var Shipper\AccountSettings */
    private $account;
    /** @var Shipper\BankData */
    private $bankData;
    /** @var Shipper\Contact */
    private $contact;
    /** @var Shipper\Contact */
    private $returnReceiver;

    /**
     * Shipper constructor.
     * @param Shipper\AccountSettings $account
     * @param Shipper\BankData $bankData
     * @param Shipper\Contact $contact
     * @param Shipper\Contact $returnReceiver
     */
    public function __construct(
        Shipper\AccountSettings $account, Shipper\BankData $bankData,
        Shipper\Contact $contact, Shipper\Contact $returnReceiver
    ) {
        $this->account = $account;
        $this->bankData = $bankData;
        $this->contact = $contact;
        $this->returnReceiver = $returnReceiver;
    }

    /**
     * @return Shipper\AccountSettings
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @return Shipper\BankData
     */
    public function getBankData()
    {
        return $this->bankData;
    }

    /**
     * @return Shipper\Contact
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @return Shipper\Contact
     */
    public function getReturnReceiver()
    {
        return $this->returnReceiver;
    }
}
