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
 * @package   Dhl\Versenden\Config
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
namespace Dhl\Versenden\Config;
use Dhl\Versenden\Config\Shipper\Account;
use Dhl\Versenden\Config\Shipper\BankData;
use Dhl\Versenden\Config\Shipper\Contact;

/**
 * Service
 *
 * @category Dhl
 * @package  Dhl\Versenden\Config
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Shipper
{
    /** @var Account */
    public $account;
    /** @var BankData */
    public $bankData;
    /** @var Contact */
    public $contact;
    /** @var Contact */
    public $returnReceiver;

    public function __construct(
        Account $account,
        BankData $bankData,
        Contact $contact,
        Contact $returnReceiver)
    {
        $this->account = $account;
        $this->bankData = $bankData;
        $this->contact = $contact;
        $this->returnReceiver = $returnReceiver;
    }
}
