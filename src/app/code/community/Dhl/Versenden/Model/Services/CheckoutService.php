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
 * @author    Andreas Müller <andreas.mueller@netresearch.de>
 * @copyright 2018 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.netresearch.de/
 */

/**
 * Dhl_Versenden_Model_Services_CheckoutService
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Andreas Müller <andreas.mueller@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Model_Services_CheckoutService
{
    const TIMEFORMAT = 'Y-m-d H:i:s';

    const API_RESPONSE_CACHE_IDENT = 'pmApiResponse';

    /**
     * @var Dhl_Versenden_Model_Webservice_Gateway_Rest
     */
    protected $client;

    /**
     * @var Dhl_Versenden_Model_Services_Startdate
     */
    protected $startDateModel;

    /**
     * @var Dhl_Versenden_Model_Config
     */
    protected $config;

    /**
     * @var Dhl_Versenden_Model_Config_Service
     */
    protected $serviceConfig;

    /**
     * @var Mage_Core_Model_Date
     */
    protected $dateModel;

    /**
     * @var Mage_Sales_Model_Quote | Mage_Sales_Model_Order
     */
    protected $quote;

    /**
     * @var string[]
     */
    protected $services = array(
        \Dhl\Versenden\Bcs\Api\Shipment\Service\PreferredDay::CODE,
        \Dhl\Versenden\Bcs\Api\Shipment\Service\PreferredTime::CODE,
        \Dhl\Versenden\Bcs\Api\Shipment\Service\PreferredLocation::CODE,
        \Dhl\Versenden\Bcs\Api\Shipment\Service\PreferredNeighbour::CODE

    );

    /**
     * @var null|\Dhl\Versenden\Cig\Model\AvailableServicesMap
     */
    protected $serviceResponse = null;

    /**
     * Dhl_Versenden_Model_Services_CheckoutService constructor.
     *
     * @param mixed[] $params
     */
    public function __construct(array $params = array())
    {
        if (array_key_exists('quote', $params)) {
            $this->quote = $params['quote'];
        }

        $this->client = Mage::getModel('dhl_versenden/webservice_gateway_rest');
        $this->startDateModel = Mage::getModel('dhl_versenden/services_startdate');
        $this->config = Mage::getModel('dhl_versenden/config');
        $this->serviceConfig = Mage::getModel('dhl_versenden/config_service');
        $this->dateModel = Mage::getSingleton('core/date');
    }

    /**
     * @return \Dhl\Versenden\Cig\Model\AvailableServicesMap
     * @throws Exception
     */
    public function getRecipientZipAvailableServices()
    {
        $startDate = $this->getStartDate();
        $zip = $this->getZipCode();
        if ($this->serviceResponse === null) {
            try {
                $this->serviceResponse = $this->loadFromCache($startDate, $zip);
            } catch (Mage_Core_Exception $e) {
                $this->serviceResponse = $this->client->checkoutRecipientZipAvailableServicesGet($startDate, $zip);
                $this->saveToCache($this->serviceResponse, $startDate, $zip);
            }
        }

        return $this->serviceResponse;
    }

    /**
     * @return string
     * @throws Exception
     */
    protected function getStartDate()
    {
        $store = $this->quote->getStore()->getId();
        $noDropOffDays = $this->config->getExcludedDropOffDays($store);
        $cutOffTime = $this->getCutOffTime($store);
        $currentDate = $this->getCurrentDate();
        $startDate = $this->startDateModel->getStartdate($currentDate, $cutOffTime, $noDropOffDays);

        return $startDate;
    }

    /**
     * @return string
     */
    protected function getZipCode()
    {
        return $this->quote->getShippingAddress()->getPostcode();
    }

    /**
     * @return mixed
     */
    protected function getCurrentDate()
    {
        $currDate = $this->dateModel->date(self::TIMEFORMAT);

        return $currDate;
    }

    /**
     * @return \Dhl\Versenden\Cig\Model\PreferredDayAvailable
     * @throws Exception
     */
    public function getPreferredDay()
    {
        return $this->getRecipientZipAvailableServices()->getPreferredDay();
    }

    /**
     * @return \Dhl\Versenden\Cig\Model\ServiceAvailable
     * @throws Exception
     */
    public function getPreferredLocation()
    {
        return $this->getRecipientZipAvailableServices()->getPreferredLocation();
    }

    /**
     * @return \Dhl\Versenden\Cig\Model\ServiceAvailable
     * @throws Exception
     */
    public function getPreferredNeighbour()
    {
        return $this->getRecipientZipAvailableServices()->getPreferredNeighbour();
    }

    /**
     * @return \Dhl\Versenden\Cig\Model\PreferredTimeAvailable
     * @throws Exception
     */
    public function getPreferredTime()
    {
        return $this->getRecipientZipAvailableServices()->getPreferredTime();
    }

    /**
     * @param $service
     * @return bool|\Dhl\Versenden\Cig\Model\ModelInterface
     */
    public function getService($service)
    {
        $method = 'get'.ucfirst($service);
        if (is_callable(array($this, $method), false)) {
            return $this->{$method}();
        }

        return false;
    }

    /**
     * @param int $store
     * @return int
     */
    protected function getCutOffTime($store)
    {
        /**
         * @var Mage_Core_Model_Date $dateModel
         */
        $dateModel = Mage::getSingleton('core/date');
        $cutOffTime = $this->serviceConfig->getCutOffTime($store);
        $cutOffTime = $dateModel->gmtTimestamp(str_replace(',', ':', $cutOffTime));

        return $cutOffTime;
    }

    /**
     * @param string $startDate
     * @param string $zip
     * @return \Dhl\Versenden\Cig\Model\AvailableServicesMap
     * @throws Mage_Core_Exception
     */
    protected function loadFromCache($startDate, $zip)
    {
        $cacheId = $this->getCacheKey($startDate, $zip);
        $cacheData = Mage::app()->getCache()->load($cacheId);
        if (!$cacheData) {
            Mage::throwException('No cached data found.');
        }

        $unserializedData = unserialize($cacheData);
        if (!isset($unserializedData[self::API_RESPONSE_CACHE_IDENT])) {
            Mage::throwException('No cached data found.');
        }

        $map = $unserializedData[self::API_RESPONSE_CACHE_IDENT];

        if (!$map instanceof \Dhl\Versenden\Cig\Model\AvailableServicesMap) {
            Mage::throwException('No cached data found.');
        }

        return $map;
    }

    /**
     * @param \Dhl\Versenden\Cig\Model\AvailableServicesMap $data
     * @param string $startDate
     * @param string $zip
     * @throws Zend_Cache_Exception
     */
    protected function saveToCache(\Dhl\Versenden\Cig\Model\AvailableServicesMap $data, $startDate, $zip)
    {
        $cacheData = serialize(array(self::API_RESPONSE_CACHE_IDENT => $data));
        Mage::app()->getCache()->save(
            $cacheData,
            $this->getCacheKey($startDate, $zip),
            array('pmapi_cache'),
            60 * 60 * 12
        );
    }

    /**
     * @param string $startDate
     * @param string $zip
     * @return string
     */
    private function getCacheKey($startDate, $zip)
    {
        $mode = $this->config->isSandboxModeEnabled() ? 'sandbox' : 'production';
        $dateString = (new DateTime($startDate))->format('Y-m-d');

        return implode('_', array($dateString, $mode, $zip));
    }
}
