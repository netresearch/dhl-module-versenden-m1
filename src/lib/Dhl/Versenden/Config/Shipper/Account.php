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
 * @package   Dhl_Versenden
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
namespace Dhl\Versenden\Config\Shipper;
use Dhl\Versenden\Config\Shipper\Account\Participation;
/**
 * Account
 *
 * @category Dhl
 * @package  Dhl\Versenden\Shipper
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Account
{
    /** @var string */
    public $user;
    /** @var string */
    public $signature;
    /** @var string */
    public $ekp;
    /** @var bool */
    public $goGreen;
    /** @var Participation */
    public $participation;

    /**
     * Account constructor.
     * @param string[] $carrierConfig
     */
    public function __construct($carrierConfig = array())
    {
        if (!empty($carrierConfig) && isset($carrierConfig['sandbox_mode'])) {

            if ($carrierConfig['sandbox_mode']) {
                $this->user = $carrierConfig['sandbox_account_user'];
                $this->signature = $carrierConfig['sandbox_account_signature'];
                $this->ekp = $carrierConfig['sandbox_account_ekp'];
                $this->goGreen = (bool)$carrierConfig['sandbox_account_gogreen_enabled'];
            } else {
                $this->user = $carrierConfig['account_user'];
                $this->signature = $carrierConfig['account_signature'];
                $this->ekp = $carrierConfig['account_ekp'];
                $this->goGreen = (bool)$carrierConfig['account_gogreen_enabled'];
            }

            $this->participation = new Participation($carrierConfig);

        }
    }
}
