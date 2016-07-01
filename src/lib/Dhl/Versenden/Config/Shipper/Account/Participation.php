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
use Dhl\Versenden\Config as ConfigReader;
use Dhl\Versenden\Config\Data as ConfigData;
use Dhl\Versenden\Config\Exception as ConfigException;
/**
 * Account
 *
 * @category Dhl
 * @package  Dhl\Versenden\Shipper\Account
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Participation extends ConfigData
{
    /** @var string */
    public $dhlPaket;
    /** @var string */
    public $dhlReturnShipment;

    /**
     * Shift data from config array to properties.
     *
     * @param ConfigReader $reader
     */
    public function loadValues(ConfigReader $reader)
    {
        if ($reader->getValue('sandbox_mode', '1')) {
            $this->dhlPaket          = $reader->getValue('sandbox_account_participation_dhlpaket');
            $this->dhlReturnShipment = $reader->getValue('sandbox_account_participation_returnshipment');
        } else {
            $this->dhlPaket          = $reader->getValue('account_participation_dhlpaket');
            $this->dhlReturnShipment = $reader->getValue('account_participation_returnshipment');
        }
    }

    /**
     * Validate values in addition to system.xml frontend validation
     *
     * @param ConfigReader $reader
     * @throws ConfigException
     */
    public function validateValues(ConfigReader $reader)
    {
        $reader->validateLength('Participation', $this->dhlPaket, 2, 2);
        $reader->validateLength('Participation', $this->dhlReturnShipment, 2, 2);
    }
}
