<?php

/**
 * See LICENSE.md for license details.
 */

use \Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder\ServiceSelection;
use \Dhl\Versenden\Bcs\Api\Shipment\Service;

class Dhl_Versenden_Model_Webservice_Builder_Service
{
    /** @var Dhl_Versenden_Model_Config_Shipper */
    protected $_shipperConfig;

    /** @var Dhl_Versenden_Model_Config_Shipment */
    protected $_shipmentConfig;

    /** @var Dhl_Versenden_Model_Config_Service */
    protected $_serviceConfig;

    /**
     * Dhl_Versenden_Model_Webservice_Builder_Service constructor.
     * @param stdClass[] $args
     * @throws Mage_Core_Exception
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

        $this->_shipperConfig = $args[$argName];

        $argName = 'shipment_config';
        if (!isset($args[$argName])) {
            Mage::throwException("required argument missing: $argName");
        }

        if (!$args[$argName] instanceof Dhl_Versenden_Model_Config_Shipment) {
           Mage::throwException("invalid argument: $argName");
        }

        $this->_shipmentConfig = $args[$argName];

        $argName = 'service_config';
        if (!isset($args[$argName])) {
            Mage::throwException("required argument missing: $argName");
        }

        if (!$args[$argName] instanceof Dhl_Versenden_Model_Config_Service) {
            Mage::throwException("invalid argument: $argName");
        }

        $this->_serviceConfig = $args[$argName];
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

        $preferredDay = !empty($selectedServices[Service\PreferredDay::CODE])
            ? $serviceDetails[Service\PreferredDay::CODE]
            : false;

        $preferredTime = !empty($selectedServices[Service\PreferredTime::CODE])
            ? $serviceDetails[Service\PreferredTime::CODE]
            : false;

        $preferredLocation = !empty($selectedServices[Service\PreferredLocation::CODE])
            ? $serviceDetails[Service\PreferredLocation::CODE]
            : false;

        $preferredNeighbour = !empty($selectedServices[Service\PreferredNeighbour::CODE])
            ? $serviceDetails[Service\PreferredNeighbour::CODE]
            : false;

        $parcelAnnouncement = !empty($selectedServices[Service\ParcelAnnouncement::CODE])
            ? (bool)$selectedServices[Service\ParcelAnnouncement::CODE]
            : false;

        $visualCheckOfAge = !empty($selectedServices[Service\VisualCheckOfAge::CODE])
            ? $serviceDetails[Service\VisualCheckOfAge::CODE]
            : false;

        $returnShipment = !empty($selectedServices[Service\ReturnShipment::CODE])
            ? (bool)$selectedServices[Service\ReturnShipment::CODE]
            : false;

        $insurance = !empty($selectedServices[Service\Insurance::CODE])
            ? number_format(
                $salesEntity->getBaseGrandTotal(),
                2,
                '.',
                ''
            )
            : false;

        $bulkyGoods = !empty($selectedServices[Service\BulkyGoods::CODE])
            ? (bool)$selectedServices[Service\BulkyGoods::CODE]
            : false;

        $parcelOutletRouting = false;
        if (!empty($selectedServices[Service\ParcelOutletRouting::CODE])) {
            $email = $this->_serviceConfig->getParcelOutletNotificationEmail($salesEntity->getStoreId());
            $parcelOutletRouting = $email ? $email : $salesEntity->getShippingAddress()->getEmail();
        }

        $paymentMethod = $salesEntity->getPayment()->getMethod();
        $cod = $this->_shipmentConfig->isCodPaymentMethod($paymentMethod, $salesEntity->getStoreId())
            ? number_format($salesEntity->getBaseGrandTotal(), 2, '.', '')
            : false;

        $printOnlyIfCodeable = !empty($selectedServices[Service\PrintOnlyIfCodeable::CODE])
            ? (bool)$selectedServices[Service\PrintOnlyIfCodeable::CODE]
            : false;

        return new ServiceSelection(
            $preferredDay,
            $preferredTime,
            $preferredLocation,
            $preferredNeighbour,
            $parcelAnnouncement,
            $visualCheckOfAge,
            $returnShipment,
            $insurance,
            $bulkyGoods,
            $parcelOutletRouting,
            $cod,
            $printOnlyIfCodeable
        );
    }
}
