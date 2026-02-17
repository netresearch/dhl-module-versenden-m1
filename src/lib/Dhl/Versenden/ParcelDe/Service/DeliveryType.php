<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\ParcelDe\Service;

class DeliveryType extends Type\Radio
{
    public const CODE = 'deliveryType';
    public const LABEL = 'Delivery Type';

    public const ECONOMY = 'ECONOMY';
    public const PREMIUM = 'PREMIUM';
    public const CDP = 'CDP';

    /**
     * Countries eligible for Closest Drop Point (CDP) delivery.
     * Per DHL Parcel DE business rules, CDP is available for these EU destinations only.
     */
    public const CDP_ELIGIBLE_COUNTRIES = ['AT', 'BE', 'BG', 'DK', 'FI', 'FR', 'HU', 'PL', 'SE'];
}
