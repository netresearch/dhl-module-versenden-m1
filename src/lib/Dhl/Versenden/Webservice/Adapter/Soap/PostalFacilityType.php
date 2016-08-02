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
 * @package   Dhl\Versenden\Webservice\Soap
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
namespace Dhl\Versenden\Webservice\Adapter\Soap;
use Dhl\Bcs\Api as VersendenApi;
use Dhl\Bcs\Api\CountryType;
use Dhl\Versenden\Webservice\RequestData;
use Dhl\Versenden\Webservice\RequestData\ShipmentOrder\Receiver;

/**
 * PackStationType
 *
 * @category Dhl
 * @package  Dhl\Versenden\Webservice\Soap
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class PostalFacilityType implements RequestType
{
    /**
     * @param Receiver\PostalFacility $requestData
     * @return VersendenApi\PackStationType|VersendenApi\PostfilialeType|VersendenApi\ParcelShopType
     */
    public static function prepare(RequestData $requestData = null)
    {
        if ($requestData instanceof Receiver\Packstation) {
            $countryType = new CountryType();
            $countryType->setCountry($requestData->getCountry());
            $countryType->setCountryISOCode($requestData->getCountryISOCode());
            $countryType->setState($requestData->getState());

            return new VersendenApi\PackStationType(
                $requestData->getPackstationNumber(),
                $requestData->getZip(),
                $requestData->getCity(),
                $countryType
            );
        }

        if ($requestData instanceof Receiver\Postfiliale) {
            return new VersendenApi\PostfilialeType(
                $requestData->getPostfilialNumber(),
                $requestData->getPostNumber(),
                $requestData->getZip(),
                $requestData->getCity()
            );
        }

        if ($requestData instanceof Receiver\ParcelShop) {
            return new VersendenApi\ParcelShopType(
                $requestData->getParcelShopNumber(),
                $requestData->getZip(),
                $requestData->getCity()
            );
        }

        return null;
    }
}
