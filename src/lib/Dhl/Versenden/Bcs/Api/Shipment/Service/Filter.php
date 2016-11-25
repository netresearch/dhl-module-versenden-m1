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
 * @package   Dhl\Versenden\Bcs\Api\Service
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
namespace Dhl\Versenden\Bcs\Api\Shipment\Service;
use Dhl\Versenden\Bcs\Api\Product;
use Dhl\Versenden\Bcs\Api\Shipment\Service\Type\Generic as ServiceItem;

/**
 * Filter
 *
 * @category Dhl
 * @package  Dhl\Versenden\Bcs\Api\Service
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
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
                PreferredLocation::CODE,
                PreferredNeighbour::CODE,
                PrintOnlyIfCodeable::CODE,
                ReturnShipment::CODE,
                VisualCheckOfAge::CODE,
                PreferredDay::CODE,
                PreferredTime::CODE
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
            Product::CODE_PAKET_AUSTRIA => [
                BulkyGoods::CODE,
                Cod::CODE,
                Insurance::CODE,
                ParcelAnnouncement::CODE,
                PrintOnlyIfCodeable::CODE,
            ],
            Product::CODE_PAKET_CONNECT => [
                BulkyGoods::CODE,
                Cod::CODE,
                Insurance::CODE,
                ParcelAnnouncement::CODE,
                PrintOnlyIfCodeable::CODE,
            ],
            Product::CODE_PAKET_INTERNATIONAL => [
                BulkyGoods::CODE,
                Insurance::CODE,
                ParcelAnnouncement::CODE,
                PrintOnlyIfCodeable::CODE,
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
                // skip services that are not meant to be combined with postal facilitites
                continue;
            }

            $shippingProducts = array_combine($this->shippingProducts, $this->shippingProducts);
            $productsServices = array_intersect_key($this->productsServices, $shippingProducts);
            $productsContainService = array_reduce(
                $productsServices,
                function ($carry, $productServices) use ($service) {
                    return $carry && in_array($service->getCode(), $productServices);
                },
                true
            );

            if (!$productsContainService) {
                // skip services that are not available in ALL requested products
                continue;
            }

            // all filters passed, add service
            $services[]= $service;
        }

        $serviceCollection->setItems($services);
        return $serviceCollection;
    }
}
