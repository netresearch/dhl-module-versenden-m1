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
use \Dhl\Versenden\Webservice\RequestData\ShipmentOrder;
use \Dhl\Versenden\Webservice\RequestData\ShipmentOrder\Settings\GlobalSettings;

/**
 * Dhl_Versenden_Model_Config_Shipment
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Model_Config_Shipment extends Dhl_Versenden_Model_Config
{
    const CONFIG_XML_FIELD_PRINTONLYIFCODABLE = 'shipment_printonlyifcodable';
    const CONFIG_XML_FIELD_UNITOFMEASURE = 'shipment_unitofmeasure';
    const CONFIG_XML_FIELD_PRODUCTWEIGHT = 'shipment_defaultweight';
    const CONFIG_XML_FIELD_CODCHARGE = 'shipment_codcharge';
    const CONFIG_XML_FIELD_DHLMETHODS = 'shipment_dhlmethods';
    const CONFIG_XML_FIELD_CODMETHODS = 'shipment_dhlcodmethods';

    /**
     * @param mixed $store
     * @return GlobalSettings
     */
    public function getSettings($store = null)
    {
        $printOnlyIfCodable = $this->getStoreConfigFlag(self::CONFIG_XML_FIELD_PRINTONLYIFCODABLE, $store);
        $unitOfMeasure = $this->getStoreConfig(self::CONFIG_XML_FIELD_UNITOFMEASURE, $store);
        $productWeight = $this->getStoreConfig(self::CONFIG_XML_FIELD_PRODUCTWEIGHT, $store);
        $codCharge = $this->getStoreConfig(self::CONFIG_XML_FIELD_CODCHARGE, $store);

        $shippingMethods = $this->getStoreConfig(self::CONFIG_XML_FIELD_DHLMETHODS, $store);
        if (empty($shippingMethods)) {
            $shippingMethods = array();
        } else {
            $shippingMethods = explode(',', $shippingMethods);
        }

        $codPaymentMethods = $this->getStoreConfig(self::CONFIG_XML_FIELD_CODMETHODS, $store);
        if (empty($codPaymentMethods)) {
            $codPaymentMethods = array();
        } else {
            $codPaymentMethods = explode(',', $codPaymentMethods);
        }

        return new GlobalSettings(
            $printOnlyIfCodable,
            $unitOfMeasure,
            $productWeight,
            $shippingMethods,
            $codPaymentMethods,
            $codCharge,
            ShipmentOrder::LABEL_TYPE_B64
        );
    }

    /**
     * Check if the given shipping method should be processed with DHL Versenden.
     *
     * @param string $shippingMethod
     * @return bool
     */
    public function canProcessMethod($shippingMethod)
    {
        return in_array($shippingMethod, $this->getSettings()->getShippingMethods());
    }

}
