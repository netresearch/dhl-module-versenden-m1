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
use Dhl\Versenden\Config\Data as ConfigData;
use Dhl\Versenden\Config as ConfigReader;
/**
 * Account
 *
 * @category Dhl
 * @package  Dhl\Versenden\Shipper
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Settings extends ConfigData
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
     * Shift data from config array to properties.
     *
     * @param ConfigReader $reader
     */
    public function loadValues(ConfigReader $reader)
    {
        $this->printOnlyIfCodable = (bool)$reader->getValue('shipment_printonlyifcodable', '1');
        $this->unitOfMeasure      = $reader->getValue('shipment_unitofmeasure', 'G');
        $this->productWeight      = (float)$reader->getValue('shipment_defaultweight', '200');
        $this->codCharge          = (float)$reader->getValue('shipment_codcharge', '2');

        if (empty($reader->getValue('shipment_dhlmethods'))) {
            $this->shippingMethods = array();
        } else {
            $this->shippingMethods = explode(',', $reader->getValue('shipment_dhlmethods'));
        }

        if (empty($reader->getValue('shipment_dhlcodmethods'))) {
            $this->codPaymentMethods = array();
        } else {
            $this->codPaymentMethods = explode(',', $reader->getValue('shipment_dhlcodmethods'));
        }
    }
}
