<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\ParcelDe\Service;

use Dhl\Versenden\ParcelDe\Product;
use Dhl\Versenden\ParcelDe\Service\Type\Generic as ServiceItem;

class Filter
{
    /**
     * All services available per product
     * @var array<string, string[]>
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
     * Shipper country code
     * @var string
     */
    protected $shipperCountry = '';

    /**
     * Recipient country code
     * @var string
     */
    protected $recipientCountry = '';

    /**
     * Create the valid mappings of
     * - services to products
     * - services to postal facility
     */
    protected function initFilters()
    {
        $matrix = new ProductServiceMatrix();
        $this->productsServices = $matrix->getMatrix();

        // Postal facility services are a separate filtering axis from the product-service
        // matrix — they restrict which services are allowed when delivering to Packstation,
        // Postfiliale, etc., regardless of product.
        $this->postalFacilityServices = [
            AdditionalInsurance::CODE,
            Cod::CODE, // up to 1500 euro
            ParcelAnnouncement::CODE,
            ReturnShipment::CODE,
        ];
    }

    /**
     * Filter constructor.
     * @param string[] $shippingProducts Products that may be used for the current shipment
     * @param bool $isPostalFacility Indicates whether current shipment is delivered to postal facility
     * @param bool $onlyCustomerServices Indicates whether only customer services are allowed or not
     * @param string $shipperCountry Shipper country code (optional, for route validation)
     * @param string $recipientCountry Recipient country code (optional, for route validation)
     */
    public function __construct(
        $shippingProducts,
        $isPostalFacility,
        $onlyCustomerServices,
        $shipperCountry = '',
        $recipientCountry = ''
    ) {
        $this->shippingProducts = $shippingProducts;
        $this->isPostalFacility = $isPostalFacility;
        $this->onlyCustomerServices = $onlyCustomerServices;
        $this->shipperCountry = $shipperCountry;
        $this->recipientCountry = $recipientCountry;

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
                false,
            );

            if (!$productsContainService) {
                // skip services that are not available in ANY of the requested products
                continue;
            }

            // Route validation (country-specific restrictions)
            if (!$this->isServiceAllowedForRoute($service->getCode())) {
                // skip services that are not allowed for this route
                continue;
            }

            // all filters passed, add service
            $services[] = $service;
        }

        $serviceCollection->setItems($services);
        return $serviceCollection;
    }

    /**
     * Check if a service is allowed for the current shipping route
     *
     * @param string $serviceCode
     * @return bool
     */
    protected function isServiceAllowedForRoute($serviceCode)
    {
        $isDomestic = !empty($this->shipperCountry) && !empty($this->recipientCountry)
            && $this->shipperCountry === $this->recipientCountry;

        switch ($serviceCode) {
            case PostalDeliveryDutyPaid::CODE:
                // pDDP for CH, GB, NO, US destinations
                // US required for Sep 2025 auto-enable (DHLGKP-343)
                // Requires route information
                if (empty($this->recipientCountry)) {
                    return false;
                }
                return in_array($this->recipientCountry, ['CH', 'GB', 'NO', 'US']);

            case Endorsement::CODE:
            case DeliveryType::CODE:
                // These services only for international shipments (non-domestic)
                // Requires route information
                if (empty($this->shipperCountry) || empty($this->recipientCountry)) {
                    return false;
                }
                return !$isDomestic;

            case GoGreenPlus::CODE:
                // GoGreen Plus available for both domestic and international shipments
                // Requires DE origin
                if (empty($this->shipperCountry)) {
                    return false;
                }
                return $this->shipperCountry === 'DE';

            case NamedPersonOnly::CODE:
            case SignedForByRecipient::CODE:
                // These services only for domestic DE shipments
                // Requires route information
                if (empty($this->shipperCountry) || empty($this->recipientCountry)) {
                    return false;
                }
                return $isDomestic && $this->shipperCountry === 'DE';

            case BulkyGoods::CODE:
                // BulkyGoods requires DE origin
                // Requires route information
                if (empty($this->shipperCountry)) {
                    return false;
                }
                return $this->shipperCountry === 'DE';

            case ClosestDropPoint::CODE:
                // CDP only for international shipments to eligible EU countries
                if (empty($this->shipperCountry) || empty($this->recipientCountry)) {
                    return false;
                }
                return !$isDomestic
                    && in_array($this->recipientCountry, DeliveryType::CDP_ELIGIBLE_COUNTRIES, true);

            default:
                // All other services allowed for any route (backward compatibility)
                // Services without explicit route restrictions can be shown even if
                // route information is unavailable
                return true;
        }
    }
}
