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
 * @author    Max Melzer<max.melzer@netresearch.de>
 * @copyright 2018 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.netresearch.de/
 */

use \Dhl\Versenden\Bcs\Api\Shipment\Service;

/**
 * Processes an offline ServiceCollection with data from the DHL Checkout Service API.
 * Removes unavailable Services.
 * Augments services with additional data (preferred day and time options).
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Max Melzer<max.melzer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Model_Services_Processor
{
    /**
     * @var Mage_Sales_Model_Quote|Mage_Sales_Model_Order
     */
    private $quote;

    /**
     * Dhl_Versenden_Model_Services_Processor constructor.
     *
     * @param mixed[] $params
     */
    public function __construct(array $params = array())
    {
        if (isset($params['quote'])) {
            $this->quote = $params['quote'];
        }
    }


    /**
     * @param Service\Collection $availableServices
     * @return Service\Collection
     */
    public function processServices(Service\Collection $availableServices)
    {
        try {
            $this->augmentServicesWithApiData($availableServices);
        } catch (Exception $exception) {
            Mage::log(
                'Could not load DHL checkout shipping services: ' . $exception->getMessage(),
                Zend_Log::ERR,
                'dhl_service.log'
            );
            $this->removeOfflineServices($availableServices);
        }
        if ($this->hasBackOrderedProducts() && $availableServices->getItem(Service\PreferredDay::CODE)) {
            $availableServices->removeItem(Service\PreferredDay::CODE);
        }

        return $availableServices;
    }


    /**
     * Retrieve service data from API and merge it with the locally provided available services.
     * Modifies the input Service\Collection object.
     *
     * @param Service\Collection $availableServices
     * @throws Exception When the API call fails
     */
    protected function augmentServicesWithApiData(Service\Collection $availableServices)
    {
        /** @var Dhl_Versenden_Model_Services_ServiceOptions $serviceOptions */
        $serviceOptions = Mage::getModel('dhl_versenden/services_serviceOptions');
        /** @var Dhl_Versenden_Model_Services_CheckoutService $apiServices */
        $apiServices = Mage::getModel(
            'dhl_versenden/services_checkoutService',
            array('quote' => $this->quote)
        );

        /** Load data from API. */
        $apiServices->getRecipientZipAvailableServices();

        foreach ($availableServices->getItems() as $serviceName => $service) {
            $code = $service->getCode();

            /** Only process services that came from the API. */
            if (!in_array($serviceName, \Dhl\Versenden\Cig\Model\AvailableServicesMap::attributeMap(), true)) {
                continue;
            }

            /** Remove services that are not availiable according to the API */
            if (!$apiServices->getService($code)->getAvailable()) {
                $availableServices->removeItem($serviceName);
                continue;
            }

            /** Set Prefferred Day or Time options from API */
            if (in_array($code, array(Service\PreferredDay::CODE, Service\PreferredTime::CODE), true)) {
                $options = $serviceOptions->getOptions($apiServices->getService($code));
                $availableServices->getItem($code)->setOptions($options);
            }
        }
    }

    /**
     * Modifies the input Service\Collection object to remove services that should not be displayed without
     * additional information from the API.
     *
     * @param Service\Collection $availableServices
     */
    protected function removeOfflineServices(Service\Collection $availableServices)
    {
        $onlineOnlyServices = array(
            Service\PreferredDay::CODE,
            Service\PreferredTime::CODE
        );

        foreach ($availableServices->getItems() as $key => $service) {
            $code = $service->getCode();
            if (in_array($code, $onlineOnlyServices, true)) {
                $availableServices->removeItem($key);
            }
        }
    }

    /**
     * Check if items contain backordered items (items with qty === 0).
     *
     * @return bool
     */
    protected function hasBackOrderedProducts()
    {
        $result = false;
        foreach ($this->quote->getAllItems() as $item) {
            /** @var Mage_Sales_Model_Quote_Item $item */
            $stockItem = $item->getProduct()->getData('stock_item');
            $qty = $item->getParentItemId() ? (float)$item->getParentItem()->getQty() : (float)$item->getQty();
            $children = $item->getChildren();

            if (empty($children) &&
                ((float)$stockItem->getQty() === 0.0 || $qty >= (float)$stockItem->getQty())
            ) {
                $result = true;
            }
        }

        return $result;
    }
}
