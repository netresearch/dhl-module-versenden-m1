<?php

/**
 * See LICENSE.md for license details.
 */

use \Dhl\Versenden\Bcs\Api\Shipment\Service;

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
        $apiServices = Mage::getSingleton(
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
