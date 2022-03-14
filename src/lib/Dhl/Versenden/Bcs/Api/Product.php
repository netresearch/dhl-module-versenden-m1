<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api;

class Product
{
    const CODE_PAKET_NATIONAL           = 'V01PAK';
    const CODE_WARENPOST_NATIONAL       = 'V62WP';
    const CODE_WELTPAKET                = 'V53WPAK';
    const CODE_EUROPAKET                = 'V54EPAK';
    const CODE_WARENPOST_INTERNATIONAL  = 'V66WPI';
    const CODE_KURIER_TAGGLEICH         = 'V06TG';
    const CODE_KURIER_WUNSCHZEIT        = 'V06WZ';

    const PROCEDURE_PAKET_NATIONAL          = '01';
    const PROCEDURE_WARENPOST_NATIONAL      = '62';
    const PROCEDURE_WELTPAKET               = '53';
    const PROCEDURE_EUROPAKET               = '54';
    const PROCEDURE_WARENPOST_INTERNATIONAL = '66';
    const PROCEDURE_KURIER_TAGGLEICH        = '01';
    const PROCEDURE_KURIER_WUNSCHZEIT       = '01';
    const PROCEDURE_RETURNSHIPMENT_NATIONAL = '07';

    /**
     * Obtain all product codes.
     *
     * @return string[]
     */
    public static function getCodes()
    {
        return [
            self::CODE_PAKET_NATIONAL,
            self::CODE_WARENPOST_NATIONAL,
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
        $procedures = array(
            self::CODE_PAKET_NATIONAL => self::PROCEDURE_PAKET_NATIONAL,
            self::CODE_WARENPOST_NATIONAL => self::PROCEDURE_WARENPOST_NATIONAL,
            self::CODE_WELTPAKET => self::PROCEDURE_WELTPAKET,
            self::CODE_EUROPAKET => self::PROCEDURE_EUROPAKET,
            self::CODE_WARENPOST_INTERNATIONAL => self::PROCEDURE_WARENPOST_INTERNATIONAL,
            self::CODE_KURIER_TAGGLEICH => self::PROCEDURE_KURIER_TAGGLEICH,
            self::CODE_KURIER_WUNSCHZEIT => self::PROCEDURE_KURIER_WUNSCHZEIT,
        );

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
        $procedures = array(
            self::CODE_PAKET_NATIONAL => self::PROCEDURE_RETURNSHIPMENT_NATIONAL,
            self::CODE_WARENPOST_NATIONAL => self::PROCEDURE_RETURNSHIPMENT_NATIONAL,
        );

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
            self::CODE_WARENPOST_NATIONAL,
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
