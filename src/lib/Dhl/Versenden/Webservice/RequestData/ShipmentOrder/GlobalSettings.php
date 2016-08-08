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
 * GlobalSettings
 *
 * @category Dhl
 * @package  Dhl\Versenden\Webservice\RequestData
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class GlobalSettings extends RequestData
{
    /** @var bool */
    private $printOnlyIfCodable;
    /** @var string  */
    private $unitOfMeasure;
    /** @var float */
    private $productWeight;
    /** @var string[] */
    private $shippingMethods;
    /** @var string[] */
    private $codPaymentMethods;
    /** @var float */
    private $codCharge;
    /** @var string */
    private $labelType;

    /**
     * GlobalSettings constructor.
     * @param bool $printOnlyIfCodable
     * @param string $unitOfMeasure
     * @param float $productWeight
     * @param \string[] $shippingMethods
     * @param \string[] $codPaymentMethods
     * @param float $codCharge
     * @param string $labelType
     */
    public function __construct(
        $printOnlyIfCodable, $unitOfMeasure, $productWeight,
        array $shippingMethods, array $codPaymentMethods, $codCharge, $labelType
    ) {
        $this->printOnlyIfCodable = $printOnlyIfCodable;
        $this->unitOfMeasure = $unitOfMeasure;
        $this->productWeight = $productWeight;
        $this->shippingMethods = $shippingMethods;
        $this->codPaymentMethods = $codPaymentMethods;
        $this->codCharge = $codCharge;
        $this->labelType = $labelType;
    }

    /**
     * @return boolean
     */
    public function isPrintOnlyIfCodable()
    {
        return $this->printOnlyIfCodable;
    }

    /**
     * @return string
     */
    public function getUnitOfMeasure()
    {
        return $this->unitOfMeasure;
    }

    /**
     * @return float
     */
    public function getProductWeight()
    {
        return $this->productWeight;
    }

    /**
     * @return \string[]
     */
    public function getShippingMethods()
    {
        return $this->shippingMethods;
    }

    /**
     * @return \string[]
     */
    public function getCodPaymentMethods()
    {
        return $this->codPaymentMethods;
    }

    /**
     * @return float
     */
    public function getCodCharge()
    {
        return $this->codCharge;
    }

    /**
     * @return string
     */
    public function getLabelType()
    {
        return $this->labelType;
    }
}
