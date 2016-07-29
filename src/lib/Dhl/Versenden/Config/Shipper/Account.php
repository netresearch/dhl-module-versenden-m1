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
use Dhl\Versenden\Config\Shipper\Account\Participation;
/**
 * Account
 *
 * @deprecated
 * @see \Dhl\Versenden\Webservice\RequestData\ShipmentOrder\Shipper\AccountSettings
 * @category Dhl
 * @package  Dhl\Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Account extends ConfigData
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
     * Shift data from config array to properties.
     *
     * @param ConfigReader $reader
     */
    public function loadValues(ConfigReader $reader)
    {
        $sandBoxMode = $reader->getValue('sandbox_mode', '1');

        if ($sandBoxMode) {
            $this->user      = $reader->getValue('sandbox_account_user');
            $this->signature = $reader->getValue('sandbox_account_signature');
            $this->ekp       = $reader->getValue('sandbox_account_ekp');
            $this->goGreen   = (bool)$reader->getValue('sandbox_account_gogreen_enabled');
        } else {
            $this->user      = $reader->getValue('account_user');
            $this->signature = $reader->getValue('account_signature');
            $this->ekp       = $reader->getValue('account_ekp');
            $this->goGreen   = (bool)$reader->getValue('account_gogreen_enabled');
        }

        $this->participation = new Participation($reader);
    }

    /**
     * Validate values in addition to system.xml frontend validation
     *
     * @param ConfigReader $reader
     * @throws ConfigException
     */
    public function validateValues(ConfigReader $reader)
    {
        $reader->validateLength('EKP', $this->ekp, 10, 10);
    }
}
