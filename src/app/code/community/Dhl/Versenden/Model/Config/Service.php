<?php

/**
 * See LICENSE.md for license details.
 */

use Dhl\Versenden\Bcs\Api\Shipment\Service;

class Dhl_Versenden_Model_Config_Service extends Dhl_Versenden_Model_Config
{
    const CONFIG_XML_FIELD_PREFERREDDAY = 'service_preferredday_enabled';
    const CONFIG_XML_FIELD_PREFERREDDAY_HANDLING_FEE = 'service_preferredday_handling_fee';
    const CONFIG_XML_FIELD_PREFERREDDAY_HANDLING_FEE_TEXT = 'service_preferredday_handling_fee_text';
    const CONFIG_XML_FIELD_CUTOFFTIME = 'service_cutoff_time';
    const CONFIG_XML_FIELD_PREFERREDLOCATION = 'service_preferredlocation_enabled';
    const CONFIG_XML_FIELD_PREFERREDLOCATION_PLACEHOLDER = 'service_preferredlocation_placeholder';
    const CONFIG_XML_FIELD_PREFERREDNEIGHBOUR = 'service_preferredneighbour_enabled';
    const CONFIG_XML_FIELD_PREFERREDNEIGHBOUR_PLACEHOLDER = 'service_preferredneighbour_placeholder';
    const CONFIG_XML_FIELD_PARCELANNOUNCEMENT = 'service_parcelannouncement_enabled';

    const CONFIG_XML_PATH_SHIPMENT_DEFAULT_VISUALCHECKOFAGE = 'shipment_autocreate_service_visualcheckofage';
    const CONFIG_XML_PATH_SHIPMENT_DEFAULT_RETURNSHIPMENT = 'shipment_autocreate_service_returnshipment';
    const CONFIG_XML_PATH_SHIPMENT_DEFAULT_INSURANCE = 'shipment_autocreate_service_insurance';
    const CONFIG_XML_PATH_SHIPMENT_DEFAULT_BULKYGOODS = 'shipment_autocreate_service_bulkygoods';
    const CONFIG_XML_PATH_SHIPMENT_DEFAULT_PARCELOUTLETROUTING = 'shipment_autocreate_service_parceloutletrouting';
    const CONFIG_XML_PATH_SHIPMENT_DEFAULT_PARCELOUTLETROUTING_NOTIFICATION_EMAIL = 'shipment_autocreate_service_parceloutletrouting_notification_email';

    /**
     * @param null $store
     * @return Service\PreferredDay
     * @throws Exception
     */
    protected function initPreferredDay($store = null)
    {
        $name              = Mage::helper('dhl_versenden/data')->__("Preferred day") .
            Mage::helper('dhl_versenden/data')->__(": Delivery at your preferred day");
        $isAvailable       = $this->getStoreConfigFlag(self::CONFIG_XML_FIELD_PREFERREDDAY, $store);
        $cutOffTime        = $this->getStoreConfig(self::CONFIG_XML_FIELD_CUTOFFTIME, $store);
        $isSelected        = false;
        $options           = array();
        $gmtSaveTimeFormat = "Y-m-d 12:00:00";

        $holidayCheck = new Mal_Holidays();
        /** @var Mage_Core_Model_Date $dateModel */
        $dateModel  = Mage::getSingleton('core/date');
        $start      = $dateModel->date("Y-m-d H:i:s");
        $cutOffTime = $dateModel->gmtTimestamp(str_replace(',', ':', $cutOffTime));

        $startDate  = ($cutOffTime < $dateModel->gmtTimestamp($start)) ? 3 : 2;
        $endDate    = $startDate + 5;

        for ($i = $startDate; $i < $endDate; $i++) {
            $date      = new DateTime($start);
            $tmpDate   = $date->add(new DateInterval("P{$i}D"));
            $checkDate = $tmpDate->format($gmtSaveTimeFormat);
            $tmpDate   = $tmpDate->format("Y-m-d");
            $disabled  = false;
            if (($dateModel->gmtDate('N', strtotime($checkDate)) == 7)
                || $holidayCheck::isHoliday($checkDate)
                || ($dateModel->gmtDate('N', strtotime($checkDate)) == 1 && $startDate == $i)
            ) {
                $endDate++;
                $disabled = true;
            }

            $options[$tmpDate] =
                array(
                    'value'    => $dateModel->gmtDate("d-", $checkDate) .
                        Mage::helper('dhl_versenden/data')->__($dateModel->gmtDate("D", $checkDate)),
                    'disabled' => $disabled
                );
        }

        $shipment = Mage::registry('current_shipment');
        // Only for Backend rendering with selected da
        if ($shipment && $shipment->getShippingAddress()) {
            /** @var Dhl\Versenden\Bcs\Api\Info $versendenInfo */
            $versendenInfo = $shipment->getShippingAddress()
                                      ->getData('dhl_versenden_info');
            if ($versendenInfo && $versendenInfo->getServices()->{Service\PreferredDay::CODE}
                && !array_key_exists($versendenInfo->getServices()->{Service\PreferredDay::CODE}, $options)
            ) {
                // Sanity check for invalid time formats
                try {
                    $selectedValue           = $versendenInfo->getServices()->{Service\PreferredDay::CODE};
                    $tmpDate                 = new DateTime($selectedValue);
                    $tmpDate                 = $dateModel
                        ->gmtDate($gmtSaveTimeFormat, $tmpDate->format($gmtSaveTimeFormat));
                    $options[$selectedValue] =
                        array(
                            'value'    => $dateModel->gmtDate("d-", $tmpDate) .
                                Mage::helper('dhl_versenden/data')->__($dateModel->gmtDate("D", $tmpDate)),
                            'disabled' => false
                        );
                } catch (Exception $e) {
                    $options[$selectedValue] = "";
                }
            }
        }

        return new Service\PreferredDay($name, $isAvailable, $isSelected, $options);
    }

    /**
     * @param mixed $store
     *
     * @return Service\PreferredLocation
     */
    protected function initPreferredLocation($store = null)
    {
        $name        = Mage::helper('dhl_versenden/data')->__("Preferred location") .
            Mage::helper('dhl_versenden/data')->__(": Delivery to your preferred drop-off location");
        $isAvailable = $this->getStoreConfigFlag(self::CONFIG_XML_FIELD_PREFERREDLOCATION, $store);
        $isSelected  = false;
        $placeholder = $this->getStoreConfig(self::CONFIG_XML_FIELD_PREFERREDLOCATION_PLACEHOLDER, $store);
        $placeholder = Mage::helper('dhl_versenden/data')->__($placeholder);

        return new Service\PreferredLocation($name, $isAvailable, $isSelected, $placeholder);
    }

    /**
     * @param mixed $store
     *
     * @return Service\PreferredNeighbour
     */
    protected function initPreferredNeighbour($store = null)
    {
        $name = Mage::helper('dhl_versenden/data')->__("Preferred neighbor");
        $name.= Mage::helper('dhl_versenden/data')->__(": Delivery to a neighbor of your choice");

        $isAvailable = $this->getStoreConfigFlag(self::CONFIG_XML_FIELD_PREFERREDNEIGHBOUR, $store);
        $isSelected  = false;
        $placeholder = $this->getStoreConfig(self::CONFIG_XML_FIELD_PREFERREDNEIGHBOUR_PLACEHOLDER, $store);
        $placeholder = Mage::helper('dhl_versenden/data')->__($placeholder);

        return new Service\PreferredNeighbour($name, $isAvailable, $isSelected, $placeholder);
    }

    /**
     * @param mixed $store
     *
     * @return Service\ParcelAnnouncement
     */
    protected function initParcelAnnouncement($store = null)
    {
        $name        = Mage::helper('dhl_versenden/data')->__("Parcel announcement");
        $isAvailable = $this->getStoreConfig(self::CONFIG_XML_FIELD_PARCELANNOUNCEMENT, $store);
        $isSelected  = false;

        return new Service\ParcelAnnouncement($name, $isAvailable, $isSelected);
    }

    /**
     * @param mixed $store
     *
     * @return Service\VisualCheckOfAge
     */
    protected function initVisualCheckOfAge($store = null)
    {
        $name        = Mage::helper('dhl_versenden/data')->__("Visual Check of Age");
        $isAvailable = true;
        $isSelected  = $this->getStoreConfig(self::CONFIG_XML_PATH_SHIPMENT_DEFAULT_VISUALCHECKOFAGE, $store);
        $options     = array(
            Service\VisualCheckOfAge::A16 => Service\VisualCheckOfAge::A16,
            Service\VisualCheckOfAge::A18 => Service\VisualCheckOfAge::A18,
        );

        return new Service\VisualCheckOfAge($name, $isAvailable, $isSelected, $options);
    }

    /**
     * @param mixed $store
     *
     * @return Service\ReturnShipment
     */
    protected function initReturnShipment($store = null)
    {
        $name        = Mage::helper('dhl_versenden/data')->__("Return Shipment");
        $isAvailable = true;
        $isSelected  = $this->getStoreConfigFlag(self::CONFIG_XML_PATH_SHIPMENT_DEFAULT_RETURNSHIPMENT, $store);

        return new Service\ReturnShipment($name, $isAvailable, $isSelected);
    }

    /**
     * @param mixed $store
     *
     * @return Service\Insurance
     */
    protected function initInsurance($store = null)
    {
        $name        = Mage::helper('dhl_versenden/data')->__("Additional Insurance");
        $isAvailable = true;
        $isSelected  = $this->getStoreConfigFlag(self::CONFIG_XML_PATH_SHIPMENT_DEFAULT_INSURANCE, $store);

        return new Service\Insurance($name, $isAvailable, $isSelected);
    }

    /**
     * @param mixed $store
     *
     * @return Service\BulkyGoods
     */
    protected function initBulkyGoods($store = null)
    {
        $name        = Mage::helper('dhl_versenden/data')->__("Bulky Goods");
        $isAvailable = true;
        $isSelected  = $this->getStoreConfigFlag(self::CONFIG_XML_PATH_SHIPMENT_DEFAULT_BULKYGOODS, $store);

        return new Service\BulkyGoods($name, $isAvailable, $isSelected);
    }

    /**
     * @param mixed $store
     *
     * @return Service\ParcelOutletRouting
     */
    protected function initParcelOutletRouting($store = null)
    {
        $name        = Mage::helper('dhl_versenden/data')->__("Parcel Outlet Routing");
        $isAvailable = true;
        $isSelected  = $this->getStoreConfigFlag(self::CONFIG_XML_PATH_SHIPMENT_DEFAULT_PARCELOUTLETROUTING, $store);

        return new Service\ParcelOutletRouting($name, $isAvailable, $isSelected);
    }

    /**
     * @param mixed $store
     *
     * @return Service\PrintOnlyIfCodeable
     */
    protected function initPrintOnlyIfCodeable($store = null)
    {
        $name        = Mage::helper('dhl_versenden/data')->__("Address Validation");
        $isAvailable = true;
        $isSelected  = $this->getStoreConfigFlag(
            Dhl_Versenden_Model_Config_Shipment::CONFIG_XML_FIELD_PRINTONLYIFCODEABLE,
            $store
        );

        return new Dhl\Versenden\Bcs\Api\Shipment\Service\PrintOnlyIfCodeable($name, $isAvailable, $isSelected);
    }

    /**
     * Load all DHL additional service models.
     *
     * @param mixed $store
     *
     * @return Service\Collection
     */
    public function getServices($store = null)
    {
        $collection = new Service\Collection();

        // customer/checkout services
        $preferredDay = $this->initPreferredDay($store);
        $collection->addItem($preferredDay);

        $preferredLocation = $this->initPreferredLocation($store);
        $collection->addItem($preferredLocation);

        $preferredNeighbour = $this->initPreferredNeighbour($store);
        $collection->addItem($preferredNeighbour);

        $parcelAnnouncement = $this->initParcelAnnouncement($store);
        $collection->addItem($parcelAnnouncement);

        // merchant/admin services
        $visualCheckOfAge = $this->initVisualCheckOfAge($store);
        $collection->addItem($visualCheckOfAge);

        $returnShipment = $this->initReturnShipment($store);
        $collection->addItem($returnShipment);

        $insurance = $this->initInsurance($store);
        $collection->addItem($insurance);

        $bulkyGoods = $this->initBulkyGoods($store);
        $collection->addItem($bulkyGoods);

        $parcelOutletRouting = $this->initParcelOutletRouting($store);
        $collection->addItem($parcelOutletRouting);

        // implicit/config services
        $printOnlyIfCodeable = $this->initPrintOnlyIfCodeable($store);
        $collection->addItem($printOnlyIfCodeable);

        return $collection;
    }

    /**
     * Obtain the service objects that are enabled via module configuration.
     *
     * @param mixed $store
     *
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
     *
     * @return Service\Collection
     */
    public function getAutoCreateServices($store = null)
    {
        // obtain all enabled services but skip services disabled for auto creation.
        $services = array_filter(
            $this->getEnabledServices($store)->getItems(),
            function (\Dhl\Versenden\Bcs\Api\Shipment\Service\Type\Generic $service) {
                return $service->isSelected();
            }
        );

        return new Service\Collection($services);
    }

    /**
     * Obtain the service objects that are enabled via module configuration and
     * applicable to the given order parameters.
     *
     * @param string $shipperCountry
     * @param string $recipientCountry
     * @param bool   $isPostalFacility
     * @param bool   $onlyCustomerServices
     * @param mixed  $store
     *
     * @return Service\Collection
     */
    public function getAvailableServices(
        $shipperCountry,
        $recipientCountry,
        $isPostalFacility,
        $onlyCustomerServices = false,
        $store = null
    ) {
        $services = $this->getEnabledServices($store);

        $euCountries = explode(',', Mage::getStoreConfig(Mage_Core_Helper_Data::XML_PATH_EU_COUNTRIES_LIST, $store));
        $shippingProducts = \Dhl\Versenden\Bcs\Api\Product::getCodesByCountry(
            $shipperCountry,
            $recipientCountry,
            $euCountries
        );

        $filter = new \Dhl\Versenden\Bcs\Api\Shipment\Service\Filter(
            $shippingProducts,
            $isPostalFacility,
            $onlyCustomerServices
        );

        return $filter->filterServiceCollection($services);
    }

    /**
     * Obtain preferred day handling fee from config.
     *
     * @param mixed $store
     * @return float
     */
    public function getPrefDayFee($store = null)
    {
        return (float) $this->getStoreConfig(self::CONFIG_XML_FIELD_PREFERREDDAY_HANDLING_FEE, $store);
    }

    /**
     * Obtain pref day handling fee text from config.
     *
     * @param mixed $store
     * @return string
     */
    public function getPrefDayHandlingFeeText($store = null)
    {
        $text = '';
        $fee  = $this->getPrefDayFee($store);
        if ($fee > 0) {
            $formattedFee = Mage::helper('core')->currency($fee, true, false);
            $text = str_replace(
                '$1',
                '<strong>' . $formattedFee . '</strong>',
                $this->getStoreConfig(self::CONFIG_XML_FIELD_PREFERREDDAY_HANDLING_FEE_TEXT, $store)
            );
        }

        return $text;
    }


    /**
     * @param mixed $store
     * @return mixed
     */
    public function getCutOffTime($store = null)
    {
        return $this->getStoreConfig(self::CONFIG_XML_FIELD_CUTOFFTIME, $store);
    }

    /**
     * @param mixed $store
     * @return string
     */
    public function getParcelOutletNotificationEmail($store = null)
    {
        return (string) $this->getStoreConfig(
            self::CONFIG_XML_PATH_SHIPMENT_DEFAULT_PARCELOUTLETROUTING_NOTIFICATION_EMAIL,
            $store
        );
    }
}

