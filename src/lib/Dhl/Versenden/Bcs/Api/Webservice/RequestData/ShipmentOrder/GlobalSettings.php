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
namespace Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder;
use Dhl\Versenden\Bcs\Api\Webservice\RequestData;

/**
 * GlobalSettings
 *
 * @category Dhl
 * @package  Dhl\Versenden\Bcs\Api\Webservice\RequestData
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class GlobalSettings extends RequestData
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
     * @param \string[] $shippingMethods
     * @param \string[] $codPaymentMethods
     * @param string $labelType
     */
    public function __construct(
        $printOnlyIfCodeable, $unitOfMeasure,
        array $shippingMethods, array $codPaymentMethods, $labelType
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
     * @return string
     */
    public function getLabelType()
    {
        return $this->labelType;
    }
}
