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
     * @param stdClass[] $args
     * @throws Mage_Core_Exception
     */
    public function __construct($args)
    {
        $argName = 'shipper_config';
        if (!isset($args[$argName])) {
            throw new Mage_Core_Exception("required argument missing: $argName");
        }
        if (!$args[$argName] instanceof Dhl_Versenden_Model_Config_Shipper) {
            throw new Mage_Core_Exception("invalid argument: $argName");
        }
        $this->shipperConfig = $args[$argName];

        $argName = 'shipment_config';
        if (!isset($args[$argName])) {
            throw new Mage_Core_Exception("required argument missing: $argName");
        }
        if (!$args[$argName] instanceof Dhl_Versenden_Model_Config_Shipment) {
            throw new Mage_Core_Exception("invalid argument: $argName");
        }
        $this->shipmentConfig = $args[$argName];
    }

    /**
     * @param Mage_Sales_Model_Order|Mage_Sales_Model_Quote $salesEntity
     * @param mixed[] $serviceInfo
     * @return ServiceSelection
     */
    public function getServiceSelection(Mage_Core_Model_Abstract $salesEntity, array $serviceInfo)
    {
        $selectedServices = $serviceInfo['shipment_service'];
        $serviceDetails = $serviceInfo['service_setting'];

        // add additional insurance service details
        $isInsurance = isset($selectedServices[\Dhl\Versenden\Shipment\Service\Insurance::CODE])
            && $selectedServices[\Dhl\Versenden\Shipment\Service\Insurance::CODE];
        if ($isInsurance) {
            $insuranceAmount = number_format($salesEntity->getBaseGrandTotal(), 2);
            $selectedServices[\Dhl\Versenden\Shipment\Service\Insurance::CODE] = $isInsurance;
            $serviceDetails[\Dhl\Versenden\Shipment\Service\Insurance::CODE] = $insuranceAmount;
        }

        // add cod service details
        $paymentMethod = $salesEntity->getPayment()->getMethod();
        $isCod = $this->shipmentConfig->isCodPaymentMethod($paymentMethod, $salesEntity->getStoreId());
        if ($isCod) {
            $codAmount = number_format($salesEntity->getBaseGrandTotal(), 2);
            $selectedServices[\Dhl\Versenden\Shipment\Service\Cod::CODE] = $isCod;
            $serviceDetails[\Dhl\Versenden\Shipment\Service\Cod::CODE] = $codAmount;
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
