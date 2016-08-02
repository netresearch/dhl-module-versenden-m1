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
namespace Dhl\Versenden\Webservice\RequestData\ShipmentOrder\Shipper;
use Dhl\Versenden\Webservice\RequestData;

/**
 * Account
 *
 * @category Dhl
 * @package  Dhl\Versenden\Webservice\RequestData
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
final class Account extends RequestData
{
    /** @var string */
    private $user;
    /** @var string */
    private $signature;
    /** @var string */
    private $ekp;
    /** @var bool */
    private $goGreen;
    /** @var string */
    private $participationDefault;
    /** @var string */
    private $participationReturn;

    /**
     * Account constructor.
     * @param string $user
     * @param string $signature
     * @param string $ekp
     * @param bool $goGreen
     * @param string $participationDefault
     * @param string $participationReturn
     */
    public function __construct($user, $signature, $ekp, $goGreen, $participationDefault, $participationReturn)
    {
        $this->validateLength('EKP', $ekp, 10, 10);
        $this->validateLength('Participation', $participationDefault, 2, 2);
        $this->validateLength('Participation', $participationReturn, 2, 2);

        $this->user = $user;
        $this->signature = $signature;
        $this->ekp = $ekp;
        $this->goGreen = $goGreen;
        $this->participationDefault = $participationDefault;
        $this->participationReturn = $participationReturn;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * @return string
     */
    public function getEkp()
    {
        return $this->ekp;
    }

    /**
     * @return boolean
     */
    public function isGoGreen()
    {
        return $this->goGreen;
    }

    /**
     * @return string
     */
    public function getParticipationDefault()
    {
        return $this->participationDefault;
    }

    /**
     * @return string
     */
    public function getParticipationReturn()
    {
        return $this->participationReturn;
    }
}
