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

    const CONFIG_XML_PATH_AUTOCREATE_VISUALCHECKOFAGE = 'shipment_autocreate_service_visualcheckofage';
    const CONFIG_XML_PATH_AUTOCREATE_RETURNSHIPMENT = 'shipment_autocreate_service_returnshipment';
    const CONFIG_XML_PATH_AUTOCREATE_INSURANCE = 'shipment_autocreate_service_insurance';
    const CONFIG_XML_PATH_AUTOCREATE_BULKYGOODS = 'shipment_autocreate_service_bulkygoods';

    /**
     * @param mixed $store
     * @return Service\DayOfDelivery
     */
    protected function initDayOfDelivery($store = null)
    {
        $name        = Mage::helper('dhl_versenden/data')->__("Day Of Delivery");
        $isAvailable = false;
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
        $isAvailable = false;
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
        $isAvailable = $this->getStoreConfig(self::CONFIG_XML_FIELD_PARCELANNOUNCEMENT, $store);
        $isSelected  = false;

        return new Service\ParcelAnnouncement($name, $isAvailable, $isSelected);
    }

    /**
     * @return Service\VisualCheckOfAge
     */
    protected function initVisualCheckOfAge()
    {
        $name        = Mage::helper('dhl_versenden/data')->__("Visual Check of Age");
        $isAvailable = true;
        $isSelected  = false;
        $options     = array(
            Service\VisualCheckOfAge::A16 => Service\VisualCheckOfAge::A16,
            Service\VisualCheckOfAge::A18 => Service\VisualCheckOfAge::A18,
        );

        return new Service\VisualCheckOfAge($name, $isAvailable, $isSelected, $options);
    }

    /**
     * @return Service\ReturnShipment
     */
    protected function initReturnShipment()
    {
        $name        = Mage::helper('dhl_versenden/data')->__("Return Shipment");
        $isAvailable = true;
        $isSelected  = false;

        return new Service\ReturnShipment($name, $isAvailable, $isSelected);
    }

    /**
     * @return Service\Insurance
     */
    protected function initInsurance()
    {
        $name        = Mage::helper('dhl_versenden/data')->__("Additional Insurance");
        $isAvailable = true;
        $isSelected  = false;

        return new Service\Insurance($name, $isAvailable, $isSelected);
    }

    /**
     * @return Service\BulkyGoods
     */
    protected function initBulkyGoods()
    {
        $name        = Mage::helper('dhl_versenden/data')->__("Bulky Goods");
        $isAvailable = true;
        $isSelected  = false;

        return new Service\BulkyGoods($name, $isAvailable, $isSelected);
    }

    /**
     * @param mixed $store
     * @return Service\PrintOnlyIfCodeable
     */
    protected function initPrintOnlyIfCodeable($store = null)
    {
        $name = Mage::helper('dhl_versenden/data')->__("Address Validation");
        $isAvailable = true;
        $isSelected  = $this->getStoreConfigFlag(
            Dhl_Versenden_Model_Config_Shipment::CONFIG_XML_FIELD_PRINTONLYIFCODEABLE,
            $store
        );

        return new Dhl\Versenden\Shipment\Service\PrintOnlyIfCodeable($name, $isAvailable, $isSelected);
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

        $printOnlyIfCodeable = $this->initPrintOnlyIfCodeable($store);
        $collection->addItem($printOnlyIfCodeable);

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
     * Obtain the service objects that are
     * - enabled via module configuration and
     * - selected for autocreate labels.
     *
     * @param mixed $store
     * @return Service\Collection
     */
    public function getAutoCreateServices($store = null)
    {
        // read autocreate service values from config
        $ageCheckValue = $this->getStoreConfig(self::CONFIG_XML_PATH_AUTOCREATE_VISUALCHECKOFAGE, $store);
        $returnShipmentValue = $this->getStoreConfigFlag(self::CONFIG_XML_PATH_AUTOCREATE_RETURNSHIPMENT, $store);
        $insuranceValue = $this->getStoreConfigFlag(self::CONFIG_XML_PATH_AUTOCREATE_INSURANCE, $store);
        $bulkyGoodsValue = $this->getStoreConfigFlag(self::CONFIG_XML_PATH_AUTOCREATE_BULKYGOODS, $store);
        $validationValue = $this->getStoreConfigFlag(
            Dhl_Versenden_Model_Config_Shipment::CONFIG_XML_FIELD_PRINTONLYIFCODEABLE,
            $store
        );

        $autoCreateValues = array(
            Service\VisualCheckOfAge::CODE => $ageCheckValue,
            Service\ReturnShipment::CODE => $returnShipmentValue,
            Service\Insurance::CODE => $insuranceValue,
            Service\BulkyGoods::CODE => $bulkyGoodsValue,
            Service\PrintOnlyIfCodeable::CODE => $validationValue,
        );

        // obtain all enabled services
        $services = $this->getEnabledServices($store)->getItems();

        // skip services disabled for auto creation
        $items = array_filter(
            $services,
            function (Service\Type\Generic $service) use ($autoCreateValues) {
                return (isset($autoCreateValues[$service->getCode()]) && $autoCreateValues[$service->getCode()]);
            }
        );

        // set autocreate service details to remaining services
        $collection = new Service\Collection($items);
        $selection  = ShipmentOrder\ServiceSelection::fromArray($autoCreateValues);
        $this->setServiceValues($collection, $selection);

        return $collection;
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
        /** @var Service\Type\Generic $service */
        foreach ($services as $service) {
            $service->setValue($serviceSelection->getServiceValue($service->getCode()));
        }
    }
}
