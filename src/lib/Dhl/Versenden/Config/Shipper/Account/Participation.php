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
namespace Dhl\Versenden\Config\Shipper\Account;
/**
 * Account
 *
 * @category Dhl
 * @package  Dhl\Versenden\Shipper\Account
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Participation
{
    public $dhlPaket;
    public $dhlReturnShipment;

    /**
     * Participation constructor.
     * @param string[] $carrierConfig
     */
    public function __construct($carrierConfig = array())
    {
        if (!empty($carrierConfig) && isset($carrierConfig['sandbox_mode'])) {
            if ($carrierConfig['sandbox_mode']) {
                $this->dhlPaket = $carrierConfig['sandbox_account_participation_dhlpaket'];
                $this->dhlReturnShipment = $carrierConfig['sandbox_account_participation_returnshipment'];
            } else {
                $this->dhlPaket = $carrierConfig['account_participation_dhlpaket'];
                $this->dhlReturnShipment = $carrierConfig['account_participation_returnshipment'];
            }
        }
    }
}
