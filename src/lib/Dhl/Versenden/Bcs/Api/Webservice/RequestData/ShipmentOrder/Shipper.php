<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder;

use Dhl\Versenden\Bcs\Api\Webservice\RequestData;

class Shipper extends RequestData
{
    /** @var Shipper\Account */
    private $account;
    /** @var Shipper\BankData */
    private $bankData;
    /** @var Shipper\Contact */
    private $contact;
    /** @var Shipper\Contact */
    private $returnReceiver;

    /**
     * Shipper constructor.
     * @param Shipper\Account $account
     * @param Shipper\BankData $bankData
     * @param Shipper\Contact $contact
     * @param Shipper\Contact $returnReceiver
     */
    public function __construct(
        Shipper\Account $account, Shipper\BankData $bankData,
        Shipper\Contact $contact, Shipper\Contact $returnReceiver
    ) {
        $this->account = $account;
        $this->bankData = $bankData;
        $this->contact = $contact;
        $this->returnReceiver = $returnReceiver;
    }

    /**
     * @return Shipper\Account
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
