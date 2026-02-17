<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\ParcelDe\Service;

use Dhl\Versenden\ParcelDe\Product;

/**
 * Canonical mapping of DHL products to their allowed services.
 *
 * Extracted from Filter for reuse in client-side compatibility rules.
 */
class ProductServiceMatrix
{
    /** @var array<string, string[]> */
    private $matrix;

    public function __construct()
    {
        $this->matrix = [
            Product::CODE_PAKET_NATIONAL => [
                AdditionalInsurance::CODE,
                BulkyGoods::CODE,
                Cod::CODE,
                ParcelAnnouncement::CODE,
                ParcelOutletRouting::CODE,
                PreferredLocation::CODE,
                PreferredNeighbour::CODE,
                ReturnShipment::CODE,
                VisualCheckOfAge::CODE,
                PreferredDay::CODE,
                NamedPersonOnly::CODE,
                SignedForByRecipient::CODE,
                NoNeighbourDelivery::CODE,
                GoGreenPlus::CODE,
            ],
            Product::CODE_KLEINPAKET => [
                GoGreenPlus::CODE,
                ParcelAnnouncement::CODE,
                ParcelOutletRouting::CODE,
                PreferredLocation::CODE,
                PreferredNeighbour::CODE,
                ReturnShipment::CODE,
            ],
            Product::CODE_WELTPAKET => [
                AdditionalInsurance::CODE,
                BulkyGoods::CODE,
                ClosestDropPoint::CODE,
                Cod::CODE,
                ParcelAnnouncement::CODE,
                Endorsement::CODE,
                PostalDeliveryDutyPaid::CODE,
                DeliveryType::CODE,
                GoGreenPlus::CODE,
            ],
            Product::CODE_EUROPAKET => [
                AdditionalInsurance::CODE,
                ParcelAnnouncement::CODE,
                GoGreenPlus::CODE,
            ],
            Product::CODE_WARENPOST_INTERNATIONAL => [
                DeliveryType::CODE,
                ParcelAnnouncement::CODE,
                GoGreenPlus::CODE,
            ],
            Product::CODE_KURIER_TAGGLEICH => [
                AdditionalInsurance::CODE,
                ParcelAnnouncement::CODE,
                ReturnShipment::CODE,
            ],
            Product::CODE_KURIER_WUNSCHZEIT => [
                AdditionalInsurance::CODE,
                ParcelAnnouncement::CODE,
                ReturnShipment::CODE,
                NamedPersonOnly::CODE,
                SignedForByRecipient::CODE,
                GoGreenPlus::CODE,
            ],
        ];
    }

    /**
     * @return array<string, string[]>
     */
    public function getMatrix(): array
    {
        return $this->matrix;
    }

    /**
     * @param string $productCode
     * @return string[]
     */
    public function getServicesForProduct(string $productCode): array
    {
        return $this->matrix[$productCode] ?? [];
    }

    /**
     * @param string $serviceCode
     * @param string $productCode
     * @return bool
     */
    public function isServiceAllowedForProduct(string $serviceCode, string $productCode): bool
    {
        return in_array($serviceCode, $this->getServicesForProduct($productCode), true);
    }
}
