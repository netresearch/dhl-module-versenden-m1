<?php

/**
 * See LICENSE.md for license details.
 */

use Dhl\Versenden\ParcelDe\Service;

class Dhl_Versenden_Model_Services_Processor
{
    /**
     * @var Mage_Sales_Model_Quote|Mage_Sales_Model_Order
     */
    protected $quote;

    /**
     * Dhl_Versenden_Model_Services_Processor constructor.
     *
     * @param mixed[] $params
     */
    public function __construct(array $params = [])
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
        if ($this->isRecipientDomestic()) {
            try {
                $this->augmentServicesWithApiData($availableServices);
            } catch (Exception $exception) {
                Mage::log(
                    'Could not load DHL checkout shipping services: ' . $exception->getMessage(),
                    Zend_Log::ERR,
                    'dhl_service.log',
                );
                $this->removeOfflineServices($availableServices);
            }
        } else {
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
            ['quote' => $this->quote],
        );

        /** Load data from API. */
        $apiServices->getRecipientZipAvailableServices();

        foreach ($availableServices->getItems() as $serviceName => $service) {
            $code = $service->getCode();

            /** Only process services that came from the API. */
            if (!in_array($serviceName, \Dhl\Versenden\Cig\Model\AvailableServicesMap::attributeMap(), true)) {
                continue;
            }

            /** Remove services that are not available according to the API */
            $apiService = $apiServices->getService($code);
            if (!$apiService || !$apiService->getAvailable()) {
                $availableServices->removeItem($serviceName);
                continue;
            }

            /** Set Preferred Day options from API */
            if ($code === Service\PreferredDay::CODE) {
                $service = $availableServices->getItem($code);
                if ($service instanceof Service\Type\Radio) {
                    $options = $serviceOptions->getOptions($apiServices->getService($code));
                    $service->setOptions($options);
                }
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
        $onlineOnlyServices = [
            Service\PreferredDay::CODE,
        ];

        foreach ($availableServices->getItems() as $key => $service) {
            $code = $service->getCode();
            if (in_array($code, $onlineOnlyServices, true)) {
                $availableServices->removeItem($key);
            }
        }
    }

    /**
     * Check if the recipient address is in Germany (DE).
     *
     * The CIG Checkout API only supports German postcodes and the services
     * it provides (preferred day/location/neighbor) are DE-domestic only.
     *
     * @return bool
     */
    protected function isRecipientDomestic()
    {
        $shippingAddress = $this->quote->getShippingAddress();

        return $shippingAddress && $shippingAddress->getCountryId() === 'DE';
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
            /** @var Mage_Sales_Model_Quote_Item|Mage_Sales_Model_Order_Item $item */
            $stockItem = $item->getProduct()->getStockItem();
            $qty = $item->getParentItemId() ? (float) $item->getParentItem()->getQty() : (float) $item->getQty();
            if ($item instanceof Mage_Sales_Model_Quote_Item) {
                $children = $item->getChildren();
            } else {
                $children = $item->getChildrenItems();
            }

            if (empty($children) &&
                ((float) $stockItem->getQty() === 0.0 || $qty >= (float) $stockItem->getQty())
            ) {
                $result = true;
            }
        }

        return $result;
    }
}
