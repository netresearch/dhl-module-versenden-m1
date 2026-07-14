<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Test_Model_Util_PddpThresholdsTest extends EcomDev_PHPUnit_Test_Case
{
    /** One second before 2026-07-24T00:00:00+02:00 */
    public const BEFORE_CUTOVER = 1784843999;

    /** Exactly 2026-07-24T00:00:00+02:00 as unix timestamp */
    public const AT_CUTOVER = 1784844000;

    /**
     * @return mixed[][]
     */
    public function getThresholdDataProvider()
    {
        return [
            'usd_before_cutover' => ['USD', self::BEFORE_CUTOVER, 800.0],
            'eur_before_cutover' => ['EUR', self::BEFORE_CUTOVER, 680.0],
            'usd_at_cutover'     => ['USD', self::AT_CUTOVER, 2500.0],
            'eur_at_cutover'     => ['EUR', self::AT_CUTOVER, 2125.0],
            'gbp_unsupported'    => ['GBP', self::AT_CUTOVER, null],
        ];
    }

    /**
     * Assert that the active pDDP threshold switches exactly at the cutover instant.
     *
     * @test
     * @dataProvider getThresholdDataProvider
     *
     * @param string $currencyCode
     * @param int $timestamp
     * @param float|null $expected
     */
    public function forCurrency($currencyCode, $timestamp, $expected)
    {
        static::assertSame(
            $expected,
            Dhl_Versenden_Model_Util_PddpThresholds::forCurrency($currencyCode, $timestamp),
        );
    }
}
