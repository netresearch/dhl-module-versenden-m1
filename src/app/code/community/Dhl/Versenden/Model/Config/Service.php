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
use \Dhl\Versenden\Bcs\Api\Shipment\Service;

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
    const CONFIG_XML_FIELD_PREFERREDDAY = 'service_preferredday_enabled';
    const CONFIG_XML_FIELD_PREFERREDDAY_HANDLING_FEE = 'service_preferredday_handling_fee';
    const CONFIG_XML_FIELD_PREFERREDDAY_HANDLING_FEE_TEXT = 'service_preferredday_handling_fee_text';
    const CONFIG_XML_FIELD_PREFERREDTIME = 'service_preferredtime_enabled';
    const CONFIG_XML_FIELD_PREFERREDTIME_HANDLING_FEE = 'service_preferredtime_handling_fee';
    const CONFIG_XML_FIELD_PREFERREDTIME_HANDLING_FEE_TEXT = 'service_preferredtime_handling_fee_text';
    const CONFIG_XML_FIELD_CUTOFFTIME = 'service_cutoff_time';
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
    const CONFIG_XML_FIELD_PREFERREDDAYANDTIME_HANDLING_FEE =
        'service_perferreddayandtime_handling_fee';
    const CONFIG_XML_FIELD_PREFERREDDAYANDTIME_HANDLING_FEE_TEXT =
        'service_perferreddayandtime_handling_fee_text';

    const CONFIG_XML_PATH_AUTOCREATE_VISUALCHECKOFAGE = 'shipment_autocreate_service_visualcheckofage';
    const CONFIG_XML_PATH_AUTOCREATE_RETURNSHIPMENT   = 'shipment_autocreate_service_returnshipment';
    const CONFIG_XML_PATH_AUTOCREATE_INSURANCE        = 'shipment_autocreate_service_insurance';
    const CONFIG_XML_PATH_AUTOCREATE_BULKYGOODS       = 'shipment_autocreate_service_bulkygoods';

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
     * @return Service\PreferredTime
     */
    protected function initPreferredTime($store = null)
    {
        $name        = Mage::helper('dhl_versenden/data')->__("Preferred time") .
            Mage::helper('dhl_versenden/data')->__(": Delivery during your preferred time slot");
        $isAvailable = $this->getStoreConfigFlag(self::CONFIG_XML_FIELD_PREFERREDTIME, $store);
        $isSelected  = false;
        $options     = array();
        if (Mage::app()->getStore()->isAdmin() || Mage::getDesign()->getArea() == 'adminhtml') {
            $options = $options + array(
                    '10001200' => Mage::helper('dhl_versenden/data')->__('10 - 12*'),
                    '12001400' => Mage::helper('dhl_versenden/data')->__('12 - 14*'),
                    '14001600' => Mage::helper('dhl_versenden/data')->__('14 - 16*'),
                    '16001800' => Mage::helper('dhl_versenden/data')->__('16 - 18*'),
                );
        }

        $options = $options + array(
                '18002000' => Mage::helper('dhl_versenden/data')->__('18 - 20'),
                '19002100' => Mage::helper('dhl_versenden/data')->__('19 - 21'),
            );

        return new Service\PreferredTime($name, $isAvailable, $isSelected, $options);
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
        $name        = Mage::helper('dhl_versenden/data')->__("Preferred neighbor") .
            Mage::helper('dhl_versenden/data')->__(": Delivery to a neighbor of your choice");
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

        $preferredTime = $this->initPreferredTime($store);
        $collection->addItem($preferredTime);

        $preferredLocation = $this->initPreferredLocation($store);
        $collection->addItem($preferredLocation);

        $preferredNeighbour = $this->initPreferredNeighbour($store);
        $collection->addItem($preferredNeighbour);

        $parcelAnnouncement = $this->initParcelAnnouncement($store);
        $collection->addItem($parcelAnnouncement);

        // merchant/admin services
        $visualCheckOfAge = $this->initVisualCheckOfAge();
        $collection->addItem($visualCheckOfAge);

        $returnShipment = $this->initReturnShipment();
        $collection->addItem($returnShipment);

        $insurance = $this->initInsurance();
        $collection->addItem($insurance);

        $bulkyGoods = $this->initBulkyGoods();
        $collection->addItem($bulkyGoods);

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
        // read autocreate service values from config
        $ageCheckValue       = $this->getStoreConfig(self::CONFIG_XML_PATH_AUTOCREATE_VISUALCHECKOFAGE, $store);
        $returnShipmentValue = $this->getStoreConfigFlag(self::CONFIG_XML_PATH_AUTOCREATE_RETURNSHIPMENT, $store);
        $insuranceValue      = $this->getStoreConfigFlag(self::CONFIG_XML_PATH_AUTOCREATE_INSURANCE, $store);
        $bulkyGoodsValue     = $this->getStoreConfigFlag(self::CONFIG_XML_PATH_AUTOCREATE_BULKYGOODS, $store);
        $validationValue     = $this->getStoreConfigFlag(
            Dhl_Versenden_Model_Config_Shipment::CONFIG_XML_FIELD_PRINTONLYIFCODEABLE,
            $store
        );

        $autoCreateValues = array(
            Service\VisualCheckOfAge::CODE    => $ageCheckValue,
            Service\ReturnShipment::CODE      => $returnShipmentValue,
            Service\Insurance::CODE           => $insuranceValue,
            Service\BulkyGoods::CODE          => $bulkyGoodsValue,
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
        /** @var Service\Type\Generic $service */
        foreach ($services as $service) {
            if (isset($autoCreateValues[$service->getCode()])) {
                $service->setValue($autoCreateValues[$service->getCode()]);
            }
        }

        $collection = new Service\Collection($items);

        return $collection;
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
    )
    {
        $services = $this->getEnabledServices($store);

        $euCountries      =
            explode(',', Mage::getStoreConfig(Mage_Core_Helper_Data::XML_PATH_EU_COUNTRIES_LIST, $store));
        $shippingProducts =
            \Dhl\Versenden\Bcs\Api\Product::getCodesByCountry($shipperCountry, $recipientCountry, $euCountries);

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
     * @param null $store
     * @return int
     */
    public function getPrefDayFee($store = null)
    {
        return (float) $this->getStoreConfig(self::CONFIG_XML_FIELD_PREFERREDDAY_HANDLING_FEE, $store);
    }

    /**
     * Obtain prefered time handling fees from config.
     *
     * @param null $store
     * @return int
     */
    public function getPrefTimeFee($store = null)
    {
        return (float) $this->getStoreConfig(self::CONFIG_XML_FIELD_PREFERREDTIME_HANDLING_FEE, $store);
    }

    /**
     * Obtain pref day handling fee text from config.
     *
     * @param null $store
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
     * Obtain pref time handling fee text from config.
     *
     * @param null $store
     * @return string
     */
    public function getPrefTimeHandlingFeeText($store = null)
    {
        $text = '';
        $fee  = $this->getPrefTimeFee($store);
        if ($fee > 0) {
            $formattedFee = Mage::helper('core')->currency($this->getPrefTimeFee($store), true, false);
            $text = str_replace(
                '$1',
                '<strong>' . $formattedFee . '</strong>',
                $this->getStoreConfig(self::CONFIG_XML_FIELD_PREFERREDTIME_HANDLING_FEE_TEXT, $store)
            );
        }

        return $text;
    }

    /**
     * Obtain combined prefered day and time handling fees from config.
     * @param null $store
     * @return float
     */
    public function getPrefDayAndTimeFee($store = null)
    {
        return (float) $this->getStoreConfig(self::CONFIG_XML_FIELD_PREFERREDDAYANDTIME_HANDLING_FEE, $store);
    }

    /**
     * Obtain combined pref day and time handling fee text from config.
     *
     * @param null $store
     * @return string
     */
    public function getPrefDayAndTimeHandlingFeeText($store = null)
    {
        $text = '';
        $fee = $this->getPrefDayAndTimeFee($store);
        if ($fee > 0) {
            $formattedFee = Mage::helper('core')->currency($this->getPrefDayAndTimeFee($store), true, false);
            $text = str_replace(
                '$1',
                '<strong>' . $formattedFee . '</strong>',
                $this->getStoreConfig(self::CONFIG_XML_FIELD_PREFERREDDAYANDTIME_HANDLING_FEE_TEXT, $store)
            );
        }

        return $text;
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function getCutOffTime($store = null)
    {
        return $this->getStoreConfig(self::CONFIG_XML_FIELD_CUTOFFTIME, $store);
    }
}
