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
 * @package   Dhl\Versenden
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
namespace Dhl\Versenden;

/**
 * Product
 *
 * @category Dhl
 * @package  Dhl\Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Product
{
    const CODE_PAKET_NATIONAL       = 'V01PAK';
    const CODE_WELTPAKET            = 'V53WPAK';
    const CODE_EUROPAKET            = 'V54EPAK';
    const CODE_KURIER_TAGGLEICH     = 'V06TG';
    const CODE_KURIER_WUNSCHZEIT    = 'V06WZ';
    const CODE_PAKET_AUSTRIA        = 'V86PARCEL';
    const CODE_PAKET_CONNECT        = 'V87PARCEL';
    const CODE_PAKET_INTERNATIONAL  = 'V82PARCEL';

    /**
     * Obtain all product codes.
     *
     * @return string[]
     */
    public static function getCodes()
    {
        return [
            self::CODE_PAKET_NATIONAL,
            self::CODE_WELTPAKET,
            self::CODE_EUROPAKET,
            self::CODE_KURIER_TAGGLEICH,
            self::CODE_KURIER_WUNSCHZEIT,
            self::CODE_PAKET_AUSTRIA,
            self::CODE_PAKET_CONNECT,
            self::CODE_PAKET_INTERNATIONAL,
        ];
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

        if ($shipperCountry == 'AT' && $recipientCountry == 'AT') {
            return static::getCodesAtToAt();
        }

        // eu
        if ($shipperCountry == 'DE' && in_array($recipientCountry, $euCountries)) {
            return static::getCodesDeToEu();
        }

        if ($shipperCountry == 'AT' && in_array($recipientCountry, $euCountries)) {
            return static::getCodesAtToEu();
        }

        // row
        if ($shipperCountry == 'DE') {
            return static::getCodesDeToRow();
        }

        if ($shipperCountry == 'AT') {
            return static::getCodesAtToRow();
        }

        return [];
    }

    protected static function getCodesDeToDe()
    {
        return [
            self::CODE_PAKET_NATIONAL,
        ];
    }

    protected static function getCodesDeToEu()
    {
        return [
            self::CODE_WELTPAKET,
        ];
    }

    protected static function getCodesDeToRow()
    {
        return [
            self::CODE_WELTPAKET,
        ];
    }

    protected static function getCodesAtToAt()
    {
        return [
            self::CODE_PAKET_AUSTRIA,
        ];
    }

    protected static function getCodesAtToEu()
    {
        return [
            self::CODE_PAKET_CONNECT,
        ];
    }

    protected static function getCodesAtToRow()
    {
        return [
            self::CODE_PAKET_INTERNATIONAL,
        ];
    }
}
