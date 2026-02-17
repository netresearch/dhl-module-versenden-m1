<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\ParcelDe\Config\Data;

use Dhl\Versenden\ParcelDe\Config\ConfigData;

class GlobalSettings extends ConfigData
{
    /** @var bool */
    private $printOnlyIfCodeable;
    /** @var string  */
    private $unitOfMeasure;
    /** @var string[] */
    private $shippingMethods;
    /** @var string[] */
    private $codPaymentMethods;
    /** @var string */
    private $labelType;

    /**
     * GlobalSettings constructor.
     * @param bool $printOnlyIfCodeable
     * @param string $unitOfMeasure
     * @param string[] $shippingMethods
     * @param string[] $codPaymentMethods
     * @param string $labelType
     */
    public function __construct(
        $printOnlyIfCodeable,
        $unitOfMeasure,
        array $shippingMethods,
        array $codPaymentMethods,
        $labelType
    ) {
        $this->printOnlyIfCodeable = $printOnlyIfCodeable;
        $this->unitOfMeasure = $unitOfMeasure;
        $this->shippingMethods = $shippingMethods;
        $this->codPaymentMethods = $codPaymentMethods;
        $this->labelType = $labelType;
    }

    /**
     * @return boolean
     */
    public function isPrintOnlyIfCodeable()
    {
        return $this->printOnlyIfCodeable;
    }

    /**
     * @return string
     */
    public function getUnitOfMeasure()
    {
        return $this->unitOfMeasure;
    }

    /**
     * @return string[]
     */
    public function getShippingMethods()
    {
        return $this->shippingMethods;
    }

    /**
     * @return string[]
     */
    public function getCodPaymentMethods()
    {
        return $this->codPaymentMethods;
    }

    /**
     * @return string
     */
    public function getLabelType()
    {
        return $this->labelType;
    }
}
