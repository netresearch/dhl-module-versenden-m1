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
namespace Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder\Export;
use Dhl\Versenden\Bcs\Api\Webservice\RequestData;

/**
 * Position
 *
 * @category Dhl
 * @package  Dhl\Versenden\Bcs\Api\Webservice\RequestData
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Position extends RequestData
{
    /** @var int */
    private $sequenceNumber;
    /** @var string */
    private $description;
    /** @var string */
    private $countryCodeOrigin;
    /** @var string */
    private $tariffNumber;
    /** @var float */
    private $amount;
    /** @var float */
    private $netWeightInKG;
    /** @var float */
    private $value;

    /**
     * Position constructor.
     * @param int $sequenceNumber
     * @param string $description
     * @param string $countryCodeOrigin
     * @param string $tariffNumber
     * @param float $amount
     * @param float $netWeightInKG
     * @param float $value
     */
    public function __construct($sequenceNumber, $description, $countryCodeOrigin,
                                $tariffNumber, $amount, $netWeightInKG, $value)
    {
        $this->sequenceNumber = $sequenceNumber;
        $this->description = $description;
        $this->countryCodeOrigin = $countryCodeOrigin;
        $this->tariffNumber = $tariffNumber;
        $this->amount = $amount;
        $this->netWeightInKG = $netWeightInKG;
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getSequenceNumber()
    {
        return $this->sequenceNumber;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getCountryCodeOrigin()
    {
        return $this->countryCodeOrigin;
    }

    /**
     * @return string
     */
    public function getTariffNumber()
    {
        return $this->tariffNumber;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return float
     */
    public function getNetWeightInKG()
    {
        return $this->netWeightInKG;
    }

    /**
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }
}
