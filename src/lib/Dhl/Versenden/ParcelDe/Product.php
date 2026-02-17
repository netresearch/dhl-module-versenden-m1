<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\ParcelDe;

class Product
{
    public const CODE_PAKET_NATIONAL           = 'V01PAK';
    public const CODE_KLEINPAKET               = 'V62KP';
    public const CODE_WELTPAKET                = 'V53WPAK';
    public const CODE_EUROPAKET                = 'V54EPAK';
    public const CODE_WARENPOST_INTERNATIONAL  = 'V66WPI';
    public const CODE_KURIER_TAGGLEICH         = 'V06TG';
    public const CODE_KURIER_WUNSCHZEIT        = 'V06WZ';

    public const PROCEDURE_PAKET_NATIONAL          = '01';
    public const PROCEDURE_KLEINPAKET              = '62';
    public const PROCEDURE_WELTPAKET               = '53';
    public const PROCEDURE_EUROPAKET               = '54';
    public const PROCEDURE_WARENPOST_INTERNATIONAL = '66';
    public const PROCEDURE_KURIER_TAGGLEICH        = '01';
    public const PROCEDURE_KURIER_WUNSCHZEIT       = '01';
    public const PROCEDURE_RETURNSHIPMENT_NATIONAL = '07';

    /**
     * Obtain all product codes.
     *
     * @return string[]
     */
    public static function getCodes()
    {
        return [
            self::CODE_PAKET_NATIONAL,
            self::CODE_KLEINPAKET,
            self::CODE_WELTPAKET,
            self::CODE_EUROPAKET,
            self::CODE_WARENPOST_INTERNATIONAL,
            self::CODE_KURIER_TAGGLEICH,
            self::CODE_KURIER_WUNSCHZEIT,
        ];
    }

    /**
     * Obtain procedure number by product code.
     *
     * @param string $code Product code
     * @return string
     */
    public static function getProcedure($code)
    {
        $procedures = [
            self::CODE_PAKET_NATIONAL => self::PROCEDURE_PAKET_NATIONAL,
            self::CODE_KLEINPAKET => self::PROCEDURE_KLEINPAKET,
            self::CODE_WELTPAKET => self::PROCEDURE_WELTPAKET,
            self::CODE_EUROPAKET => self::PROCEDURE_EUROPAKET,
            self::CODE_WARENPOST_INTERNATIONAL => self::PROCEDURE_WARENPOST_INTERNATIONAL,
            self::CODE_KURIER_TAGGLEICH => self::PROCEDURE_KURIER_TAGGLEICH,
            self::CODE_KURIER_WUNSCHZEIT => self::PROCEDURE_KURIER_WUNSCHZEIT,
        ];

        if (!isset($procedures[$code])) {
            return '';
        }

        return $procedures[$code];
    }

    /**
     * Obtain procedure number for return shipments.
     *
     * @param string $code Product code
     * @return string
     */
    public static function getProcedureReturn($code)
    {
        $procedures = [
            self::CODE_PAKET_NATIONAL => self::PROCEDURE_RETURNSHIPMENT_NATIONAL,
            self::CODE_KLEINPAKET => self::PROCEDURE_RETURNSHIPMENT_NATIONAL,
        ];

        if (!isset($procedures[$code])) {
            return '';
        }

        return $procedures[$code];
    }

    /**
     * Obtain valid product codes by shipper and recipient country.
     *
     * @param string $shipperCountry
     * @param string $recipientCountry
     * @param string[] $euCountries
     * @return string[]
     */
    public static function getCodesByCountry($shipperCountry, $recipientCountry, $euCountries)
    {
        // domestic
        if ($shipperCountry == 'DE' && $recipientCountry == 'DE') {
            return static::getCodesDeToDe();
        }

        // eu
        if ($shipperCountry == 'DE' && in_array($recipientCountry, $euCountries)) {
            return static::getCodesDeToEu();
        }

        // row
        if ($shipperCountry == 'DE') {
            return static::getCodesDeToRow();
        }

        return [];
    }

    protected static function getCodesDeToDe()
    {
        return [
            self::CODE_PAKET_NATIONAL,
            self::CODE_KLEINPAKET,
        ];
    }

    protected static function getCodesDeToEu()
    {
        return [
            self::CODE_WELTPAKET,
            self::CODE_WARENPOST_INTERNATIONAL,
        ];
    }

    protected static function getCodesDeToRow()
    {
        return [
            self::CODE_WELTPAKET,
            self::CODE_WARENPOST_INTERNATIONAL,
        ];
    }
}
