<?php

/**
 * See LICENSE.md for license details.
 */

/**
 * pDDP value limits for the US customs territory (see UsCustomsTerritory).
 *
 * The DHL API enforces these limits server-side and switches on 2026-07-24
 * (Europe/Berlin): until 2026-07-23 shipments up to 800 USD / 680 EUR require
 * pDDP and above it is forbidden; from 2026-07-24 the boundary is
 * 2,500 USD / 2,125 EUR. Resolving at runtime lets one release behave
 * correctly on both sides of the date.
 */
class Dhl_Versenden_Model_Util_PddpThresholds
{
    public const THRESHOLD_USD_LEGACY = 800.0;
    public const THRESHOLD_EUR_LEGACY = 680.0;
    public const THRESHOLD_USD_2026 = 2500.0;
    public const THRESHOLD_EUR_2026 = 2125.0;

    /**
     * The instant the DHL API switches to the new limits (Europe/Berlin)
     */
    public const NEW_THRESHOLDS_EFFECTIVE_AT = '2026-07-24T00:00:00+02:00';

    /**
     * Resolve the pDDP threshold active at the given time for the given currency.
     *
     * @param string $currencyCode
     * @param int $timestamp unix timestamp to resolve against
     * @return float|null threshold value or null if currency not supported
     */
    public static function forCurrency($currencyCode, $timestamp)
    {
        $newThresholdsEffective = $timestamp >= strtotime(self::NEW_THRESHOLDS_EFFECTIVE_AT);

        $thresholds = [
            'USD' => $newThresholdsEffective ? self::THRESHOLD_USD_2026 : self::THRESHOLD_USD_LEGACY,
            'EUR' => $newThresholdsEffective ? self::THRESHOLD_EUR_2026 : self::THRESHOLD_EUR_LEGACY,
        ];

        return $thresholds[$currencyCode] ?? null;
    }
}
