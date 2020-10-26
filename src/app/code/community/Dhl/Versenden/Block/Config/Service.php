<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Block_Config_Service extends Mage_Core_Block_Template
{
    /**
     * @var string[]
     */
    protected $_nameArray = array(
        'preferredDay'       => 'Preferred day',
        'preferredLocation'  => 'Preferred location',
        'preferredNeighbour' => 'Preferred neighbor',
        'parcelAnnouncement' => 'Parcel announcement',
    );

    /**
     * @var Mage_Core_Model_Date
     */
    protected $coreDate;

    /**
     * @var Dhl_Versenden_Helper_Data
     */
    protected $helper;

    /**
     * @var Dhl_Versenden_Model_Config_Service
     */
    protected $serviceConfig;

    /**
     * @var Dhl_Versenden_Model_Config_Shipment $shipmentConfig
     */
    protected $shipmentConfig;

    /**
     * Dhl_Versenden_Block_Config_Service constructor.
     *
     * @param mixed[] $args
     */
    public function __construct(array $args = array())
    {
        $this->coreDate = Mage::getSingleton('core/date');
        $this->helper = Mage::helper('dhl_versenden/data');
        $this->serviceConfig = Mage::getModel('dhl_versenden/config_service');
        $this->shipmentConfig = Mage::getModel('dhl_versenden/config_shipment');

        parent::__construct($args);

        $this->setData('services', $this->loadServices());
    }

    /**
     * @param string $nameKey
     * @return string
     */
    public function renderName($nameKey)
    {
        return $this->_nameArray[$nameKey];
    }

    /**
     * @param string $value
     * @return string
     */
    public function renderDate($value)
    {
        $formatedDate = $this->coreDate->date("d.m.Y", $value);
        if (strpos(Mage::app()->getLocale()->getLocaleCode(), 'de_') === false) {
            $formatedDate = $this->coreDate->date("d/m/Y", $value);
        }

        return $formatedDate;
    }

    /**
     * check if there are services selected
     *
     * @return bool
     */
    public function isAnyServiceSelected()
    {
        $services = $this->getServices();
        $servicesArray     = is_array($services) ? $services : $services->toArray();
        $filteredServices  = array_filter($servicesArray);

        return count($filteredServices) > 0;
    }

    /**
     * @return string
     */
    public function renderFeeText()
    {
        $services = $this->getServices();
        $fee = 0;
        $text = '';
        $isCombined = false;

        if ($services->preferredDay) {
            $fee = $this->serviceConfig->getPrefDayFee();
        }

        if ($fee > 0) {
            $text = $this->getFeetext($isCombined, $fee);
        }

        return $text;
    }

    /**
     * @param bool $isCombined
     * @param int|float $fee
     * @return string
     */
    protected function getFeetext($isCombined, $fee)
    {
        $default = $this->helper->__('(The cost of %s for %s already included in the delivery costs.)');
        $formattedFee = Mage_Core_Helper_Data::currency($fee, true, false);

        $service = $isCombined ?
            $this->helper->__('your preferred delivery options') :
            $this->helper->__('your preferred delivery option');

        return sprintf($default, $formattedFee, $service);
    }

    /**
     * @return array|\Dhl\Versenden\Bcs\Api\Info\Services
     */
    protected function loadServices()
    {
        /** @var Mage_Sales_Model_Quote $quote */
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $shippingAddress = $quote->getShippingAddress();
        $shippingMethod = $shippingAddress->getShippingMethod();

        if (!$this->shipmentConfig->canProcessMethod($shippingMethod, $quote->getStoreId())) {
            return array();
        }

        /** @var \Dhl\Versenden\Bcs\Api\Info $dhlVersendenInfo */
        $dhlVersendenInfo = $shippingAddress->getData('dhl_versenden_info');
        if (!$dhlVersendenInfo instanceof \Dhl\Versenden\Bcs\Api\Info) {
            $dhlVersendenInfo = \Dhl\Versenden\Bcs\Api\Info\Serializer::unserialize($dhlVersendenInfo);
        }

        return $dhlVersendenInfo->getServices();
    }

    /**
     * @return Dhl\Versenden\Bcs\Api\Info\Services|array
     */
    public function getServices()
    {
        return $this->getData('services');
    }
}
