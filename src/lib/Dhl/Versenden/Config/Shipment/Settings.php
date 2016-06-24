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
namespace Dhl\Versenden\Config\Shipment;
/**
 * Account
 *
 * @category Dhl
 * @package  Dhl\Versenden\Shipper
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Settings
{
    /** @var bool */
    public $printOnlyIfCodable;
    /** @var string  */
    public $unitOfMeasure;
    /** @var float */
    public $productWeight;
    /** @var string[] */
    public $shippingMethods;
    /** @var string[] */
    public $codPaymentMethods;
    /** @var float */
    public $codCharge;

    /**
     * Settings constructor.
     * @param string[] $carrierConfig
     */
    public function __construct($carrierConfig = array())
    {
        if (!empty($carrierConfig)) {
            $this->printOnlyIfCodable = (bool)$carrierConfig['shipment_printonlyifcodable'];
            $this->unitOfMeasure = $carrierConfig['shipment_unitofmeasure'];
            $this->productWeight = $carrierConfig['shipment_defaultweight'];
            $this->codCharge = $carrierConfig['shipment_codcharge'];

            if (!isset($carrierConfig['shipment_dhlmethods']) || empty($carrierConfig['shipment_dhlmethods'])) {
                $this->shippingMethods = array();
            } else {
                $this->shippingMethods = explode(',', $carrierConfig['shipment_dhlmethods']);
            }

            if (!isset($carrierConfig['shipment_dhlcodmethods']) || empty($carrierConfig['shipment_dhlcodmethods'])) {
                $this->codPaymentMethods = array();
            } else {
                $this->codPaymentMethods = explode(',', $carrierConfig['shipment_dhlcodmethods']);
            }
        }
    }
}
