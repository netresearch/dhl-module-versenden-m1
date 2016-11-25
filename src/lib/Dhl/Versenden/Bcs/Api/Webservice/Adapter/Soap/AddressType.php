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
 * @package   Dhl\Versenden\Bcs\Api\Webservice\Soap
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
namespace Dhl\Versenden\Bcs\Api\Webservice\Adapter\Soap;
use Dhl\Versenden\Bcs\Soap as VersendenApi;
use Dhl\Versenden\Bcs\Api\Webservice\RequestData;

/**
 * AddressType
 *
 * @category Dhl
 * @package  Dhl\Versenden\Bcs\Api\Webservice\Soap
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class AddressType implements RequestType
{
    /**
     * @param RequestData\ShipmentOrder\Person $requestData
     * @return VersendenApi\NativeAddressType
     */
    public static function prepare(RequestData $requestData)
    {
        $countryType = new VersendenApi\CountryType($requestData->getCountryISOCode());
        $countryType->setCountry($requestData->getCountry());
        $countryType->setState($requestData->getState());

        $requestType = new VersendenApi\NativeAddressType(
            $requestData->getStreetName(),
            $requestData->getStreetNumber(),
            $requestData->getZip(),
            $requestData->getCity(),
            $countryType
        );

        return $requestType;
    }
}
