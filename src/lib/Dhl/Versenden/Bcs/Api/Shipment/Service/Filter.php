<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Shipment\Service;

use Dhl\Versenden\Bcs\Api\Product;
use Dhl\Versenden\Bcs\Api\Shipment\Service\Type\Generic as ServiceItem;

class Filter
{
    /**
     * All services available per product
     * @var string[]
     */
    protected $productsServices = [];
    /**
     * All services available for postal facility deliveries
     * @var string[]
     */
    protected $postalFacilityServices = [];

    /**
     * The products that are applicable to the current shipper/recipient addresses
     * @var string[]
     */
    protected $shippingProducts = [];
    /**
     * Postal facility delivery indicator
     * @var bool
     */
    protected $isPostalFacility = false;
    /**
     * Customer service selection indicator
     * @var bool
     */
    protected $onlyCustomerServices = false;

    /**
     * Create the valid mappings of
     * - services to products
     * - services to postal facility
     */
    protected function initFilters()
    {
        $this->productsServices = [
            Product::CODE_PAKET_NATIONAL => [
                BulkyGoods::CODE,
                Cod::CODE,
                Insurance::CODE,
                ParcelAnnouncement::CODE,
                ParcelOutletRouting::CODE,
                PreferredLocation::CODE,
                PreferredNeighbour::CODE,
                PrintOnlyIfCodeable::CODE,
                ReturnShipment::CODE,
                VisualCheckOfAge::CODE,
                PreferredDay::CODE,
                PreferredTime::CODE
            ],
            Product::CODE_WARENPOST_NATIONAL => [
                ParcelAnnouncement::CODE,
                ParcelOutletRouting::CODE,
                PreferredLocation::CODE,
                PreferredNeighbour::CODE,
                PrintOnlyIfCodeable::CODE,
            ],
            Product::CODE_WELTPAKET => [
                BulkyGoods::CODE,
                Insurance::CODE,
                ParcelAnnouncement::CODE,
                PrintOnlyIfCodeable::CODE,
            ],
            Product::CODE_EUROPAKET => [
                Insurance::CODE,
                ParcelAnnouncement::CODE,
                PrintOnlyIfCodeable::CODE,
            ],
            Product::CODE_KURIER_TAGGLEICH => [
                Insurance::CODE,
                ParcelAnnouncement::CODE,
                PrintOnlyIfCodeable::CODE,
                ReturnShipment::CODE,
            ],
            Product::CODE_KURIER_WUNSCHZEIT => [
                Insurance::CODE,
                ParcelAnnouncement::CODE,
                PrintOnlyIfCodeable::CODE,
                ReturnShipment::CODE,
            ],
        ];

        $this->postalFacilityServices = [
            Cod::CODE, // up to 1500 euro
            Insurance::CODE,
            ParcelAnnouncement::CODE,
            PrintOnlyIfCodeable::CODE,
            ReturnShipment::CODE,
        ];
    }

    /**
     * Filter constructor.
     * @param string[] $shippingProducts Products that may be used for the current shipment
     * @param bool $isPostalFacility Indicates whether current shipment is delivered to postal facility
     * @param bool $onlyCustomerServices Indicates whether only customer services are allowed or not
     */
    public function __construct($shippingProducts, $isPostalFacility, $onlyCustomerServices)
    {
        $this->shippingProducts = $shippingProducts;
        $this->isPostalFacility = $isPostalFacility;
        $this->onlyCustomerServices = $onlyCustomerServices;

        $this->initFilters();
    }

    /**
     * Filter a given service by GK API product
     *
     * @param ServiceItem $service
     * @return ServiceItem|null
     */
    public function filterService(ServiceItem $service)
    {
        $collection = new Collection([$service]);
        $filteredCollection = $this->filterServiceCollection($collection);
        return $filteredCollection->getItem($service->getCode());
    }

    /**
     * Filter a given service collection by GK API product
     *
     * @param Collection $serviceCollection
     * @return Collection
     */
    public function filterServiceCollection(Collection $serviceCollection)
    {
        $services = [];

        /** @var ServiceItem $service */
        foreach ($serviceCollection as $service) {
            if ($this->onlyCustomerServices && !$service->isCustomerService()) {
                // skip services that are not meant to be selected by customer
                continue;
            }

            if ($this->isPostalFacility && !in_array($service->getCode(), $this->postalFacilityServices)) {
                // skip services that are not meant to be combined with postal facilities
                continue;
            }

            $shippingProducts = array_combine($this->shippingProducts, $this->shippingProducts);
            $productsServices = array_intersect_key($this->productsServices, $shippingProducts);
            $productsContainService = array_reduce(
                $productsServices,
                function ($carry, $productServices) use ($service) {
                    return $carry || in_array($service->getCode(), $productServices);
                },
                false
            );

            if (!$productsContainService) {
                // skip services that are not available in ANY of the requested products
                continue;
            }

            // all filters passed, add service
            $services[]= $service;
        }

        $serviceCollection->setItems($services);
        return $serviceCollection;
    }
}
