<?php

/**
 * See LICENSE.md for license details.
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
            $this->serviceResponse = $this->client->checkoutRecipientZipAvailableServicesGet($startDate, $zip);
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
}
