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
 * @author    Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */

/**
 * Dhl_Versenden_Block_Config_Service
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Block_Config_Service extends Mage_Core_Block_Template
{
    /**
     * @var string[]
     */
    protected $_nameArray = array(
        'preferredDay'       => 'Preferred day',
        'preferredTime'      => 'Preferred time',
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
     * @param $value
     * @return string
     */
    public function renderTime($value)
    {
        if (!ctype_digit($value) || strlen($value) !== 8) {
            return $value;
        }

        $timeValues = str_split($value, 2);
        $result     = $timeValues[0] . ' - ' . $timeValues[2];

        return $this->helper->__($result);
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

        if ($services->preferredDay && $services->preferredTime) {
            $fee = $this->serviceConfig->getPrefDayAndTimeFee();
            $isCombined = true;
        } elseif ($services->preferredDay) {
            $fee = $this->serviceConfig->getPrefDayFee();
        } elseif ($services->preferredTime) {
            $fee = $this->serviceConfig->getPrefTimeFee();
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
