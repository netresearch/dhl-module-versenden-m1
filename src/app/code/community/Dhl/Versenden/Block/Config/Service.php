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
    protected $_nameArray = array(
        'preferredDay'       => 'Preferred day',
        'preferredTime'      => 'Preferred time',
        'preferredLocation'  => 'Preferred location',
        'preferredNeighbour' => 'Preferred neighbor',
        'parcelAnnouncement' => 'Parcel announcement',
    );

    public function renderName($nameKey)
    {
        return $this->_nameArray[$nameKey];
    }

    public function renderDate($value)
    {
        /** @var Mage_Core_Model_Date $dateModel */
        $dateModel = Mage::getSingleton('core/date');

        $formatedDate = $dateModel->date("d.m.Y", $value);
        if (strpos(Mage::app()->getLocale()->getLocaleCode(), 'de_') === false) {
            $formatedDate = $dateModel->date("d/m/Y", $value);
        }

        return $formatedDate;
    }

    public function renderTime($value)
    {
        if (!ctype_digit($value) || strlen($value) <> 8) {
            return $value;
        }

        $timeValues = str_split($value, 2);
        $result     = $timeValues[0] . ' - ' . $timeValues[2];

        return Mage::helper('dhl_versenden/data')->__($result);
    }

    /**
     * check if there are services selected
     *
     * @return bool
     */
    public function isServiceSelected()
    {
        $services          = $this->getData('services');
        $servicesArray     = $services->toArray();
        $filteredServices  = array_filter($servicesArray);

        return count($filteredServices) > 0 ? true : false;
    }

    /**
     * @return array|\Dhl\Versenden\Bcs\Api\Info\Services
     */
    public function getServices()
    {
        /** @var Mage_Sales_Model_Quote $quote */
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $shippingAddress = $quote->getShippingAddress();
        $shippingMethod  = $shippingAddress->getShippingMethod();

        /** @var Dhl_Versenden_Model_Config_Shipment $config */
        $config = Mage::getModel('dhl_versenden/config_shipment');
        if (!$config->canProcessMethod($shippingMethod, $quote->getStoreId())) {
            return array();
        }

        /** @var \Dhl\Versenden\Bcs\Api\Info $dhlVersendenInfo */
        $dhlVersendenInfo = $shippingAddress->getData('dhl_versenden_info');
        if (!$dhlVersendenInfo instanceof \Dhl\Versenden\Bcs\Api\Info) {
            $serializer = new \Dhl\Versenden\Bcs\Api\Info\Serializer();
            $dhlVersendenInfo = $serializer->unserialize($dhlVersendenInfo);
        }

        return $dhlVersendenInfo->getServices();
    }

    /**
     * @param $services
     * @return string
     */
    public function renderFeeText($services)
    {
        /** @var Dhl_Versenden_Model_Config_Service $config */
        $config = Mage::getModel('dhl_versenden/config_service');
        $fee = 0;
        $text = '';
        $type = 'single';

        if ($services->preferredDay && $services->preferredTime) {
            $fee = $config->getPrefDayAndTimeFee();
            $type = 'combined';
        } elseif ($services->preferredDay) {
            $fee = $config->getPrefDayFee();
        } elseif ($services->preferredTime) {
            $fee = $config->getPrefTimeFee();
        }

        if ($fee > 0) {
            $formattedFee = Mage::helper('core')->currency($fee, true, false);
            $msg = $this->getFeetext($type);
            $text = str_replace(
                '$1',
                 $formattedFee,
                $msg
            );
        }

        return $text;
    }

    /**
     * @param string$type
     * @return string
     */
    public function getFeetext($type)
    {
        $default = __('(The cost of $1 for $2 already included in the delivery costs.)');
        $service = $type === 'single' ? __('your preferred delivery option') : __('your preferred delivery options');

        $text = str_replace(
            '$2',
            $service,
            $default
        );

        return $text;
    }
}
