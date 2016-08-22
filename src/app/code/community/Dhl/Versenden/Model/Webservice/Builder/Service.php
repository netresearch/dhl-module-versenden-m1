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
use \Dhl\Versenden\Webservice\RequestData\ShipmentOrder\ServiceSelection;
/**
 * Dhl_Versenden_Model_Webservice_Builder_Service
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Model_Webservice_Builder_Service
{
    /** @var Dhl_Versenden_Model_Config_Shipper */
    protected $shipperConfig;

    /** @var Dhl_Versenden_Model_Config_Shipment */
    protected $shipmentConfig;

    /**
     * Dhl_Versenden_Model_Webservice_Builder_Service constructor.
     * @param $args
     */
    public function __construct($args)
    {
        $argName = 'shipper_config';
        if (!isset($args[$argName])) {
            Mage::throwException("required argument missing: $argName");
        }
        if (!$args[$argName] instanceof Dhl_Versenden_Model_Config_Shipper) {
            Mage::throwException("invalid argument: $argName");
        }
        $this->shipperConfig = $args[$argName];

        $argName = 'shipment_config';
        if (!isset($args[$argName])) {
            Mage::throwException("required argument missing: $argName");
        }
        if (!$args[$argName] instanceof Dhl_Versenden_Model_Config_Shipper) {
            Mage::throwException("invalid argument: $argName");
        }
        $this->shipmentConfig = $args[$argName];
    }

    /**
     * @param Mage_Sales_Model_Order_Shipment $shipment
     * @param array $serviceInfo
     * @return ServiceSelection
     */
    public function getServiceSelection(Mage_Sales_Model_Order_Shipment $shipment, array $serviceInfo)
    {
        $selectedServices = $serviceInfo['shipment_service'];
        $serviceDetails = $serviceInfo['service_setting'];

        // add additional insurance service details
        $isInsurance = isset($selectedServices[\Dhl\Versenden\Shipment\Service\Insurance::CODE])
            && $selectedServices[\Dhl\Versenden\Shipment\Service\Insurance::CODE];
        if ($isInsurance) {
            $selectedServices[\Dhl\Versenden\Shipment\Service\Insurance::CODE] = $isInsurance;
            //TODO(nr): calculate package totals from shipment
            $serviceDetails[\Dhl\Versenden\Shipment\Service\Insurance::CODE] = 99;
        }

        // add gogreen service details
        $isGoGreen = $this->shipperConfig->getAccountSettings($shipment->getStoreId())->isGoGreen();
        if ($isGoGreen) {
            $selectedServices[\Dhl\Versenden\Shipment\Service\GoGreen::CODE] = $isGoGreen;
            $serviceDetails[\Dhl\Versenden\Shipment\Service\GoGreen::CODE] = $isGoGreen;
        }

        // add cod service details
        $paymentMethod = $shipment->getOrder()->getPayment()->getMethod();
        $isCod = $this->shipmentConfig->isCodPaymentMethod($paymentMethod, $shipment->getStoreId());
        if ($isCod) {
            $selectedServices[\Dhl\Versenden\Shipment\Service\Cod::CODE] = $isCod;
            //TODO(nr): calculate package totals from shipment
            $serviceDetails[\Dhl\Versenden\Shipment\Service\Cod::CODE] = 99;
        }


        $settings = array();

        foreach ($selectedServices as $name => $isSelected) {
            if ($isSelected) {
                $settings[$name] = isset($serviceDetails[$name]) ? $serviceDetails[$name] : true;
            }
        }

        return ServiceSelection::fromArray($settings);

    }
}
