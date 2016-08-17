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
use \Dhl\Versenden\Shipment\Service;
/**
 * Dhl_Versenden_Model_Config_Service
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Model_Config_Service extends Dhl_Versenden_Model_Config
{
    const CONFIG_XML_FIELD_DAYOFDELIVERY = 'service_dayofdelivery_enabled';
    const CONFIG_XML_FIELD_DELIVERYTIMEFRAME = 'service_deliverytimeframe_enabled';
    const CONFIG_XML_FIELD_PREFERREDLOCATION = 'service_preferredlocation_enabled';
    const CONFIG_XML_FIELD_PREFERREDLOCATION_PLACEHOLDER = 'service_preferredlocation_placeholder';
    const CONFIG_XML_FIELD_PREFERREDNEIGHBOUR = 'service_preferredneighbour_enabled';
    const CONFIG_XML_FIELD_PREFERREDNEIGHBOUR_PLACEHOLDER = 'service_preferredneighbour_placeholder';
    const CONFIG_XML_FIELD_PACKSTATION = 'service_packstation_enabled';
    const CONFIG_XML_FIELD_PARCELANNOUNCEMENT = 'service_parcelannouncement_enabled';
    const CONFIG_XML_FIELD_VISUALCHECKOFAGE = 'service_visualcheckofage_enabled';
    const CONFIG_XML_FIELD_RETURNSHIPMENT = 'service_returnshipment_enabled';
    const CONFIG_XML_FIELD_INSURANCE = 'service_insurance_enabled';
    const CONFIG_XML_FIELD_BULKYGOODS = 'service_bulkygoods_enabled';

    /**
     * @param mixed $store
     * @return Service\DayOfDelivery
     */
    protected function initDayOfDelivery($store = null)
    {
        $name        = Mage::helper('dhl_versenden/data')->__("Day Of Delivery");
        $isAvailable = $this->getStoreConfigFlag(self::CONFIG_XML_FIELD_DAYOFDELIVERY, $store);
        $isSelected  = false;
        $placeholder = Mage::helper('dhl_versenden/data')->__("Select Date");

        return new Service\DayOfDelivery($name, $isAvailable, $isSelected, $placeholder);
    }

    /**
     * @param mixed $store
     * @return Service\DeliveryTimeFrame
     */
    protected function initDeliveryTimeFrame($store = null)
    {
        $name        = Mage::helper('dhl_versenden/data')->__("Delivery Time Frame");
        $isAvailable = $this->getStoreConfigFlag(self::CONFIG_XML_FIELD_DELIVERYTIMEFRAME, $store);
        $isSelected  = false;
        $options     = array(
            '10001200' => '10:00 - 12:00',
            '12001400' => '12:00 - 14:00',
            '14001600' => '14:00 - 16:00',
            '16001800' => '16:00 - 18:00',
            '18002000' => '18:00 - 20:00',
            '19002100' => '19:00 - 21:00',
        );

        return new Service\DeliveryTimeFrame($name, $isAvailable, $isSelected, $options);
    }

    /**
     * @param mixed $store
     * @return Service\PreferredLocation
     */
    protected function initPreferredLocation($store = null)
    {
        $name        = Mage::helper('dhl_versenden/data')->__("Preferred Location");
        $isAvailable = $this->getStoreConfigFlag(self::CONFIG_XML_FIELD_PREFERREDLOCATION, $store);
        $isSelected  = false;
        $placeholder = $this->getStoreConfig(self::CONFIG_XML_FIELD_PREFERREDLOCATION_PLACEHOLDER, $store);
        $placeholder = Mage::helper('dhl_versenden/data')->__($placeholder);

        return new Service\PreferredLocation($name, $isAvailable, $isSelected, $placeholder);
    }

    /**
     * @param mixed $store
     * @return Service\PreferredNeighbour
     */
    protected function initPreferredNeighbour($store = null)
    {
        $name        = Mage::helper('dhl_versenden/data')->__("Preferred Neighbour");
        $isAvailable = $this->getStoreConfigFlag(self::CONFIG_XML_FIELD_PREFERREDNEIGHBOUR, $store);
        $isSelected  = false;
        $placeholder = $this->getStoreConfig(self::CONFIG_XML_FIELD_PREFERREDNEIGHBOUR_PLACEHOLDER, $store);
        $placeholder = Mage::helper('dhl_versenden/data')->__($placeholder);

        return new Service\PreferredNeighbour($name, $isAvailable, $isSelected, $placeholder);
    }

    /**
     * @param mixed $store
     * @return Service\ParcelAnnouncement
     */
    protected function initParcelAnnouncement($store = null)
    {
        $name        = Mage::helper('dhl_versenden/data')->__("Parcel Announcement");
        $isAvailable = $this->getStoreConfigFlag(self::CONFIG_XML_FIELD_PARCELANNOUNCEMENT, $store);
        $isSelected  = false;

        return new Service\ParcelAnnouncement($name, $isAvailable, $isSelected);
    }

    /**
     * @param mixed $store
     * @return Service\VisualCheckOfAge
     */
    protected function initVisualCheckOfAge($store = null)
    {
        $name        = Mage::helper('dhl_versenden/data')->__("Visual Check of Age");
        $isAvailable = $this->getStoreConfigFlag(self::CONFIG_XML_FIELD_VISUALCHECKOFAGE, $store);
        $isSelected  = false;
        $options     = array(
            Service\VisualCheckOfAge::A16 => Service\VisualCheckOfAge::A16,
            Service\VisualCheckOfAge::A18 => Service\VisualCheckOfAge::A18,
        );

        return new Service\VisualCheckOfAge($name, $isAvailable, $isSelected, $options);
    }

    /**
     * @param mixed $store
     * @return Service\ReturnShipment
     */
    protected function initReturnShipment($store = null)
    {
        $name        = Mage::helper('dhl_versenden/data')->__("Return Shipment");
        $isAvailable = $this->getStoreConfigFlag(self::CONFIG_XML_FIELD_RETURNSHIPMENT, $store);
        $isSelected  = false;

        return new Service\ReturnShipment($name, $isAvailable, $isSelected);
    }

    /**
     * @param mixed $store
     * @return Service\Insurance
     */
    protected function initInsurance($store = null)
    {
        $name        = Mage::helper('dhl_versenden/data')->__("Additional Insurance");
        $isAvailable = $this->getStoreConfigFlag(self::CONFIG_XML_FIELD_INSURANCE, $store);
        $isSelected  = false;

        return new Service\Insurance($name, $isAvailable, $isSelected);
    }

    /**
     * @param mixed $store
     * @return Service\BulkyGoods
     */
    protected function initBulkyGoods($store = null)
    {
        $name        = Mage::helper('dhl_versenden/data')->__("Bulky Goods");
        $isAvailable = $this->getStoreConfigFlag(self::CONFIG_XML_FIELD_BULKYGOODS, $store);
        $isSelected  = false;

        return new Service\BulkyGoods($name, $isAvailable, $isSelected);
    }

    /**
     * Load all DHL additional service models.
     *
     * @param mixed $store
     * @return Service\Collection
     */
    public function getServices($store = null)
    {
        $collection = new Service\Collection();

        $dayOfDelivery = $this->initDayOfDelivery($store);
        $collection->addItem($dayOfDelivery);

        $deliveryTimeFrame = $this->initDeliveryTimeFrame($store);
        $collection->addItem($deliveryTimeFrame);

        $preferredLocation = $this->initPreferredLocation($store);
        $collection->addItem($preferredLocation);

        $preferredNeighbour = $this->initPreferredNeighbour($store);
        $collection->addItem($preferredNeighbour);

        $parcelAnnouncement = $this->initParcelAnnouncement($store);
        $collection->addItem($parcelAnnouncement);

        $visualCheckOfAge = $this->initVisualCheckOfAge($store);
        $collection->addItem($visualCheckOfAge);

        $returnShipment = $this->initReturnShipment($store);
        $collection->addItem($returnShipment);

        $insurance = $this->initInsurance($store);
        $collection->addItem($insurance);

        $bulkyGoods = $this->initBulkyGoods($store);
        $collection->addItem($bulkyGoods);

        return $collection;
    }

    /**
     * Obtain the service objects that are enabled via module configuration.
     *
     * @param mixed $store
     * @return Service\Collection
     */
    public function getEnabledServices($store = null)
    {
        $services = $this->getServices($store)->getItems();

        $items = array_filter(
            $services,
            function (Service\Type\Generic $service) {
                return (bool)$service->isEnabled();
            }
        );

        return new Service\Collection($items);
    }

    /**
     * @param Service\Collection $serviceCollection
     * @param ShipmentOrder\ServiceSelection $serviceSelection
     */
    public function setServiceValues(
        Service\Collection $serviceCollection,
        ShipmentOrder\ServiceSelection $serviceSelection
    ) {
        $services = $serviceCollection->getItems();
        array_walk(
            $services,
            function (Service\Type\Generic $service) use ($serviceSelection) {
                $service->setValue($serviceSelection->getServiceValue($service->getCode()));
            }
        );
    }
}
