<?php

/**
 * See LICENSE.md for license details.
 */

use Dhl\Versenden\ParcelDe\Service;

class Dhl_Versenden_Model_Config_Service extends Dhl_Versenden_Model_Config
{
    public const CONFIG_XML_FIELD_PREFERREDDAY = 'service_preferredday_enabled';
    public const CONFIG_XML_FIELD_PREFERREDDAY_HANDLING_FEE = 'service_preferredday_handling_fee';
    public const CONFIG_XML_FIELD_PREFERREDDAY_HANDLING_FEE_TEXT = 'service_preferredday_handling_fee_text';
    public const CONFIG_XML_FIELD_CUTOFFTIME = 'service_cutoff_time';
    public const CONFIG_XML_FIELD_PREFERREDLOCATION = 'service_preferredlocation_enabled';
    public const CONFIG_XML_FIELD_PREFERREDLOCATION_PLACEHOLDER = 'service_preferredlocation_placeholder';
    public const CONFIG_XML_FIELD_PREFERREDNEIGHBOUR = 'service_preferredneighbour_enabled';
    public const CONFIG_XML_FIELD_PREFERREDNEIGHBOUR_PLACEHOLDER = 'service_preferredneighbour_placeholder';
    public const CONFIG_XML_FIELD_PARCELANNOUNCEMENT = 'service_parcelannouncement_enabled';
    public const CONFIG_XML_FIELD_NONEIGHBOURDELIVERY = 'service_noneighbourdelivery_enabled';
    public const CONFIG_XML_FIELD_GOGREEN = 'service_gogreen_enabled';
    public const CONFIG_XML_FIELD_NONEIGHBOURDELIVERY_HANDLING_FEE = 'service_noneighbourdelivery_handling_fee';
    public const CONFIG_XML_FIELD_NONEIGHBOURDELIVERY_HANDLING_FEE_TEXT = 'service_noneighbourdelivery_handling_fee_text';
    public const CONFIG_XML_FIELD_GOGREEN_HANDLING_FEE = 'service_gogreen_handling_fee';
    public const CONFIG_XML_FIELD_GOGREEN_HANDLING_FEE_TEXT = 'service_gogreen_handling_fee_text';
    public const CONFIG_XML_FIELD_CLOSESTDROPPOINT = 'service_closestdroppoint_enabled';
    public const CONFIG_XML_FIELD_CLOSESTDROPPOINT_HANDLING_FEE = 'service_closestdroppoint_handling_fee';
    public const CONFIG_XML_FIELD_CLOSESTDROPPOINT_HANDLING_FEE_TEXT = 'service_closestdroppoint_handling_fee_text';

    public const CONFIG_XML_PATH_SHIPMENT_DEFAULT_NONEIGHBOURDELIVERY = 'shipment_autocreate_service_noneighbourdelivery';
    public const CONFIG_XML_PATH_SHIPMENT_DEFAULT_NAMEDPERSONONLY = 'shipment_autocreate_service_namedpersononly';
    public const CONFIG_XML_PATH_SHIPMENT_DEFAULT_SIGNEDFORBYRECIPIENT = 'shipment_autocreate_service_signedforbyrecipient';
    public const CONFIG_XML_PATH_SHIPMENT_DEFAULT_ENDORSEMENT = 'shipment_autocreate_service_endorsement';
    public const CONFIG_XML_PATH_SHIPMENT_DEFAULT_DELIVERYTYPE = 'shipment_autocreate_service_deliverytype';
    public const CONFIG_XML_PATH_SHIPMENT_DEFAULT_POSTALDELIVERYDUTYPAID = 'shipment_autocreate_service_postaldeliverydutypaid';
    public const CONFIG_XML_PATH_SHIPMENT_DEFAULT_VISUALCHECKOFAGE = 'shipment_autocreate_service_visualcheckofage';
    public const CONFIG_XML_PATH_SHIPMENT_DEFAULT_RETURNSHIPMENT = 'shipment_autocreate_service_returnshipment';
    public const CONFIG_XML_PATH_SHIPMENT_DEFAULT_INSURANCE = 'shipment_autocreate_service_insurance';
    public const CONFIG_XML_PATH_SHIPMENT_DEFAULT_BULKYGOODS = 'shipment_autocreate_service_bulkygoods';
    public const CONFIG_XML_PATH_SHIPMENT_DEFAULT_PARCELOUTLETROUTING = 'shipment_autocreate_service_parceloutletrouting';
    public const CONFIG_XML_PATH_SHIPMENT_DEFAULT_PARCELOUTLETROUTING_NOTIFICATION_EMAIL = 'shipment_autocreate_service_parceloutletrouting_notification_email';


    /**
     * @param null $store
     * @return Service\PreferredDay
     * @throws Exception
     */
    protected function initPreferredDay($store = null)
    {
        $name              = Mage::helper('dhl_versenden/data')->__(Service\PreferredDay::LABEL) .
            Mage::helper('dhl_versenden/data')->__(': Delivery at your preferred day');
        $isAvailable       = $this->getStoreConfigFlag(self::CONFIG_XML_FIELD_PREFERREDDAY, $store);
        $cutOffTime        = $this->getStoreConfig(self::CONFIG_XML_FIELD_CUTOFFTIME, $store);
        $isSelected        = false;
        $options           = [];
        $gmtSaveTimeFormat = 'Y-m-d 12:00:00';

        $holidayCheck = new Mal_Holidays();
        /** @var Mage_Core_Model_Date $dateModel */
        $dateModel  = Mage::getSingleton('core/date');
        $start      = $dateModel->date('Y-m-d H:i:s');
        $cutOffTime = $dateModel->gmtTimestamp(str_replace(',', ':', $cutOffTime));

        $startDate  = ($cutOffTime < $dateModel->gmtTimestamp($start)) ? 3 : 2;
        $endDate    = $startDate + 5;

        for ($i = $startDate; $i < $endDate; $i++) {
            $date      = new DateTime($start);
            $tmpDate   = $date->add(new DateInterval("P{$i}D"));
            $checkDate = $tmpDate->format($gmtSaveTimeFormat);
            $tmpDate   = $tmpDate->format('Y-m-d');
            $disabled  = false;
            if (($dateModel->gmtDate('N', strtotime($checkDate)) == 7)
                || $holidayCheck::isHoliday($checkDate)
                || ($dateModel->gmtDate('N', strtotime($checkDate)) == 1 && $startDate == $i)
            ) {
                $endDate++;
                $disabled = true;
            }

            $options[$tmpDate] =
                [
                    'value'    => $dateModel->gmtDate('d-', $checkDate) .
                        Mage::helper('dhl_versenden/data')->__($dateModel->gmtDate('D', $checkDate)),
                    'disabled' => $disabled,
                ];
        }

        $shipment = Mage::registry('current_shipment');
        // Only for Backend rendering with selected da
        if ($shipment && $shipment->getShippingAddress()) {
            /** @var Dhl\Versenden\ParcelDe\Info|null $versendenInfo */
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
                        [
                            'value'    => $dateModel->gmtDate('d-', $tmpDate) .
                                Mage::helper('dhl_versenden/data')->__($dateModel->gmtDate('D', $tmpDate)),
                            'disabled' => false,
                        ];
                } catch (Exception $e) {
                    $options[$selectedValue] = '';
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
        $name        = Mage::helper('dhl_versenden/data')->__(Service\PreferredLocation::LABEL) .
            Mage::helper('dhl_versenden/data')->__(': Delivery to your preferred drop-off location');
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
        $name = Mage::helper('dhl_versenden/data')->__(Service\PreferredNeighbour::LABEL);
        $name .= Mage::helper('dhl_versenden/data')->__(': Delivery to a neighbor of your choice');

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
        $name        = Mage::helper('dhl_versenden/data')->__(Service\ParcelAnnouncement::LABEL);
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
        $name    = Mage::helper('dhl_versenden/data')->__(Service\VisualCheckOfAge::LABEL);
        $value   = $this->getStoreConfig(self::CONFIG_XML_PATH_SHIPMENT_DEFAULT_VISUALCHECKOFAGE, $store);
        $options = [
            Service\VisualCheckOfAge::A16 => Service\VisualCheckOfAge::A16,
            Service\VisualCheckOfAge::A18 => Service\VisualCheckOfAge::A18,
        ];

        $service = new Service\VisualCheckOfAge($name, true, (bool) $value, $options);
        $service->setValue($value);

        return $service;
    }

    /**
     * @param mixed $store
     *
     * @return Service\ReturnShipment
     */
    protected function initReturnShipment($store = null)
    {
        $name       = Mage::helper('dhl_versenden/data')->__(Service\ReturnShipment::LABEL);
        $isSelected = $this->getStoreConfigFlag(self::CONFIG_XML_PATH_SHIPMENT_DEFAULT_RETURNSHIPMENT, $store);

        return new Service\ReturnShipment($name, true, $isSelected);
    }

    /**
     * @param mixed $store
     *
     * @return Service\AdditionalInsurance
     */
    protected function initInsurance($store = null)
    {
        $name = Mage::helper('dhl_versenden/data')->__(Service\AdditionalInsurance::LABEL);
        $isSelected = $this->getStoreConfigFlag(self::CONFIG_XML_PATH_SHIPMENT_DEFAULT_INSURANCE, $store);

        return new Service\AdditionalInsurance($name, true, $isSelected);
    }

    /**
     * @param mixed $store
     *
     * @return Service\BulkyGoods
     */
    protected function initBulkyGoods($store = null)
    {
        $name       = Mage::helper('dhl_versenden/data')->__(Service\BulkyGoods::LABEL);
        $isSelected = $this->getStoreConfigFlag(self::CONFIG_XML_PATH_SHIPMENT_DEFAULT_BULKYGOODS, $store);

        return new Service\BulkyGoods($name, true, $isSelected);
    }

    /**
     * @param mixed $store
     *
     * @return Service\ParcelOutletRouting
     */
    protected function initParcelOutletRouting($store = null)
    {
        $name        = Mage::helper('dhl_versenden/data')->__(Service\ParcelOutletRouting::LABEL);
        $isSelected  = $this->getStoreConfigFlag(self::CONFIG_XML_PATH_SHIPMENT_DEFAULT_PARCELOUTLETROUTING, $store);
        $placeholder = Mage::helper('dhl_versenden/data')->__('Notification Email');

        $service = new Service\ParcelOutletRouting($name, true, $isSelected, $placeholder);
        $porEmail = $this->getParcelOutletNotificationEmail($store);
        if ($porEmail) {
            $service->setDefaultValue($porEmail);
        }

        return $service;
    }

    /**
     * @param mixed $store
     *
     * @return Service\NoNeighbourDelivery
     */
    protected function initNoNeighbourDelivery($store = null)
    {
        $name        = Mage::helper('dhl_versenden/data')->__(Service\NoNeighbourDelivery::LABEL);
        $isAvailable = $this->getStoreConfigFlag(self::CONFIG_XML_FIELD_NONEIGHBOURDELIVERY, $store);
        $isSelected  = false;

        return new Service\NoNeighbourDelivery($name, $isAvailable, $isSelected);
    }

    /**
     * @param mixed $store
     *
     * @return Service\NamedPersonOnly
     */
    protected function initNamedPersonOnly($store = null)
    {
        $name       = Mage::helper('dhl_versenden/data')->__(Service\NamedPersonOnly::LABEL);
        $isSelected = $this->getStoreConfigFlag(self::CONFIG_XML_PATH_SHIPMENT_DEFAULT_NAMEDPERSONONLY, $store);

        return new Service\NamedPersonOnly($name, true, $isSelected);
    }

    /**
     * @param mixed $store
     *
     * @return Service\SignedForByRecipient
     */
    protected function initSignedForByRecipient($store = null)
    {
        $name       = Mage::helper('dhl_versenden/data')->__(Service\SignedForByRecipient::LABEL);
        $isSelected = $this->getStoreConfigFlag(self::CONFIG_XML_PATH_SHIPMENT_DEFAULT_SIGNEDFORBYRECIPIENT, $store);

        return new Service\SignedForByRecipient($name, true, $isSelected);
    }

    /**
     * @param mixed $store
     *
     * @return Service\GoGreenPlus
     */
    protected function initGoGreen($store = null)
    {
        $name        = Mage::helper('dhl_versenden/data')->__(Service\GoGreenPlus::LABEL);
        $isAvailable = $this->getStoreConfigFlag(self::CONFIG_XML_FIELD_GOGREEN, $store);
        $isSelected  = false;

        return new Service\GoGreenPlus($name, $isAvailable, $isSelected);
    }

    /**
     * @param mixed $store
     *
     * @return Service\Endorsement
     */
    protected function initEndorsement($store = null)
    {
        $name    = Mage::helper('dhl_versenden/data')->__(Service\Endorsement::LABEL);
        $value   = $this->getStoreConfig(self::CONFIG_XML_PATH_SHIPMENT_DEFAULT_ENDORSEMENT, $store);
        $options = [
            Service\Endorsement::RETURN => Mage::helper('dhl_versenden/data')->__('Return'),
            Service\Endorsement::ABANDON => Mage::helper('dhl_versenden/data')->__('Abandon'),
        ];

        $service = new Service\Endorsement($name, true, (bool) $value, $options);
        $service->setValue($value);

        return $service;
    }

    /**
     * @param mixed $store
     *
     * @return Service\DeliveryType
     */
    protected function initDeliveryType($store = null)
    {
        $name    = Mage::helper('dhl_versenden/data')->__(Service\DeliveryType::LABEL);
        $value   = $this->getStoreConfig(self::CONFIG_XML_PATH_SHIPMENT_DEFAULT_DELIVERYTYPE, $store);
        $options = [
            Service\DeliveryType::ECONOMY => Mage::helper('dhl_versenden/data')->__('Economy'),
            Service\DeliveryType::PREMIUM => Mage::helper('dhl_versenden/data')->__('Premium'),
            Service\DeliveryType::CDP => Mage::helper('dhl_versenden/data')->__('Closest Drop Point'),
        ];

        $service = new Service\DeliveryType($name, true, (bool) $value, $options);
        $service->setValue($value);

        return $service;
    }

    /**
     * @param mixed $store
     *
     * @return Service\PostalDeliveryDutyPaid
     */
    protected function initPostalDeliveryDutyPaid($store = null)
    {
        $name       = Mage::helper('dhl_versenden/data')->__(Service\PostalDeliveryDutyPaid::LABEL);
        $isSelected = $this->getStoreConfigFlag(self::CONFIG_XML_PATH_SHIPMENT_DEFAULT_POSTALDELIVERYDUTYPAID, $store);

        return new Service\PostalDeliveryDutyPaid($name, true, $isSelected);
    }

    /**
     * @param mixed $store
     *
     * @return Service\ClosestDropPoint
     */
    protected function initClosestDropPoint($store = null)
    {
        $name = Mage::helper('dhl_versenden/data')->__(Service\ClosestDropPoint::LABEL);
        $isAvailable = $this->getStoreConfigFlag(self::CONFIG_XML_FIELD_CLOSESTDROPPOINT, $store);
        $isSelected = false;

        return new Service\ClosestDropPoint($name, $isAvailable, $isSelected);
    }

    /**
     * @return Service\Cod
     */
    protected function initCod()
    {
        $name        = Mage::helper('dhl_versenden/data')->__(Service\Cod::LABEL);
        $isSelected  = false;
        $placeholder = Mage::helper('dhl_versenden/data')->__('Reason for Payment');

        return new Service\Cod($name, true, $isSelected, $placeholder, 70);
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

        $closestDropPoint = $this->initClosestDropPoint($store);
        if ($closestDropPoint->isEnabled()) {
            $collection->addItem($closestDropPoint);
        }

        // merchant/admin services (checkbox)
        $returnShipment = $this->initReturnShipment($store);
        $collection->addItem($returnShipment);

        $insurance = $this->initInsurance($store);
        $collection->addItem($insurance);

        $bulkyGoods = $this->initBulkyGoods($store);
        $collection->addItem($bulkyGoods);

        $parcelOutletRouting = $this->initParcelOutletRouting($store);
        $collection->addItem($parcelOutletRouting);

        // domestic delivery services (checkbox)
        $noNeighbourDelivery = $this->initNoNeighbourDelivery($store);
        $collection->addItem($noNeighbourDelivery);

        $namedPersonOnly = $this->initNamedPersonOnly($store);
        $collection->addItem($namedPersonOnly);

        $signedForByRecipient = $this->initSignedForByRecipient($store);
        $collection->addItem($signedForByRecipient);

        $goGreen = $this->initGoGreen($store);
        $collection->addItem($goGreen);

        // international shipping services (checkbox)
        $postalDeliveryDutyPaid = $this->initPostalDeliveryDutyPaid($store);
        $collection->addItem($postalDeliveryDutyPaid);

        // payment services (checkbox)
        $cod = $this->initCod();
        $collection->addItem($cod);

        // dropdown services (displayed last for cleaner UI)
        $visualCheckOfAge = $this->initVisualCheckOfAge($store);
        $collection->addItem($visualCheckOfAge);

        $endorsement = $this->initEndorsement($store);
        $collection->addItem($endorsement);

        $deliveryType = $this->initDeliveryType($store);
        $collection->addItem($deliveryType);

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
                return (bool) $service->isEnabled();
            },
        );

        return new Service\Collection($items);
    }

    /**
     * Obtain the service objects selected for autocreate labels.
     *
     * Uses getServices() (not getEnabledServices()) because checkout visibility
     * and shipment defaults are independent concerns: a service disabled in
     * checkout can still be auto-applied as a shipment default.
     *
     * @param mixed $store
     *
     * @return Service\Collection
     */
    public function getAutoCreateServices($store = null)
    {
        $customerAutoCreatePaths = [
            Service\NoNeighbourDelivery::CODE => self::CONFIG_XML_PATH_SHIPMENT_DEFAULT_NONEIGHBOURDELIVERY,
        ];

        $services = $this->getServices($store)->getItems();

        // Customer-facing services init with isSelected=false (for checkout/packaging).
        // For autocreate, apply the shipment default config.
        foreach ($services as $service) {
            $code = $service->getCode();
            if (isset($customerAutoCreatePaths[$code])) {
                $service->setValue((string) $this->getStoreConfigFlag($customerAutoCreatePaths[$code], $store));
            }
        }

        $items = array_filter(
            $services,
            function (Service\Type\Generic $service) {
                return (bool) $service->isSelected();
            },
        );

        return new Service\Collection($items);
    }

    /**
     * Obtain the service objects applicable to the given order parameters.
     *
     * For checkout context ($forPackaging = false): filters by checkout config toggles.
     * For packaging popup context ($forPackaging = true): shows all services regardless
     * of checkout config — admin users need override capability.
     *
     * @param string $shipperCountry
     * @param string $recipientCountry
     * @param bool   $isPostalFacility
     * @param bool   $onlyCustomerServices
     * @param mixed  $store
     * @param bool   $forPackaging
     *
     * @return Service\Collection
     */
    public function getAvailableServices(
        $shipperCountry,
        $recipientCountry,
        $isPostalFacility,
        $onlyCustomerServices = false,
        $store = null,
        $forPackaging = false
    ) {
        $services = $forPackaging
            ? $this->getServices($store)
            : $this->getEnabledServices($store);

        $euCountries = explode(',', Mage::getStoreConfig(Mage_Core_Helper_Data::XML_PATH_EU_COUNTRIES_LIST, $store));
        $shippingProducts = \Dhl\Versenden\ParcelDe\Product::getCodesByCountry(
            $shipperCountry,
            $recipientCountry,
            $euCountries,
        );

        $filter = new \Dhl\Versenden\ParcelDe\Service\Filter(
            $shippingProducts,
            $isPostalFacility,
            $onlyCustomerServices,
            $shipperCountry,
            $recipientCountry,
        );

        return $filter->filterServiceCollection($services);
    }

    /**
     * Obtain delivery day handling fee from config.
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
        return $this->formatHandlingFeeText(
            $this->getPrefDayFee($store),
            self::CONFIG_XML_FIELD_PREFERREDDAY_HANDLING_FEE_TEXT,
            $store,
        );
    }

    /**
     * Obtain CDP handling fee from config.
     *
     * @param mixed $store
     * @return float
     */
    public function getCdpFee($store = null)
    {
        return (float) $this->getStoreConfig(self::CONFIG_XML_FIELD_CLOSESTDROPPOINT_HANDLING_FEE, $store);
    }

    /**
     * Obtain CDP handling fee text from config.
     *
     * @param mixed $store
     * @return string
     */
    public function getCdpHandlingFeeText($store = null)
    {
        return $this->formatHandlingFeeText(
            $this->getCdpFee($store),
            self::CONFIG_XML_FIELD_CLOSESTDROPPOINT_HANDLING_FEE_TEXT,
            $store,
        );
    }

    /**
     * Obtain No Neighbour Delivery handling fee from config.
     *
     * @param mixed $store
     * @return float
     */
    public function getNoNeighbourDeliveryFee($store = null)
    {
        return (float) $this->getStoreConfig(self::CONFIG_XML_FIELD_NONEIGHBOURDELIVERY_HANDLING_FEE, $store);
    }

    /**
     * Obtain No Neighbour Delivery handling fee text from config.
     *
     * @param mixed $store
     * @return string
     */
    public function getNoNeighbourDeliveryHandlingFeeText($store = null)
    {
        return $this->formatHandlingFeeText(
            $this->getNoNeighbourDeliveryFee($store),
            self::CONFIG_XML_FIELD_NONEIGHBOURDELIVERY_HANDLING_FEE_TEXT,
            $store,
        );
    }

    /**
     * Obtain GoGreen handling fee from config.
     *
     * @param mixed $store
     * @return float
     */
    public function getGoGreenFee($store = null)
    {
        return (float) $this->getStoreConfig(self::CONFIG_XML_FIELD_GOGREEN_HANDLING_FEE, $store);
    }

    /**
     * Obtain GoGreen handling fee text from config.
     *
     * @param mixed $store
     * @return string
     */
    public function getGoGreenHandlingFeeText($store = null)
    {
        return $this->formatHandlingFeeText(
            $this->getGoGreenFee($store),
            self::CONFIG_XML_FIELD_GOGREEN_HANDLING_FEE_TEXT,
            $store,
        );
    }

    /**
     * Format a handling fee as display text with the fee amount substituted into the config template.
     *
     * @param float $fee
     * @param string $textConfigField
     * @param mixed $store
     * @return string
     */
    private function formatHandlingFeeText($fee, $textConfigField, $store)
    {
        if ($fee <= 0) {
            return '';
        }

        $formattedFee = Mage::helper('core')->currency($fee, true, false);
        $configText = $this->getStoreConfig($textConfigField, $store);

        return str_replace(
            '$1',
            '<strong>' . $formattedFee . '</strong>',
            Mage::helper('dhl_versenden')->__($configText),
        );
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
            $store,
        );
    }

}
