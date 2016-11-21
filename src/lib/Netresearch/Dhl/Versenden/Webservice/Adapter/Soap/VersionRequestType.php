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
 * @package   Netresearch\Dhl\Versenden\Webservice\Soap
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
namespace Netresearch\Dhl\Versenden\Webservice\Adapter\Soap;
use Netresearch\Dhl\Bcs\Api as VersendenApi;
use Netresearch\Dhl\Versenden\Webservice\RequestData;

/**
 * VersionRequestType
 *
 * @category Dhl
 * @package  Netresearch\Dhl\Versenden\Webservice\Soap
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class VersionRequestType implements RequestType
{
    /**
     * @param RequestData\Version $requestData
     * @return VersendenApi\Version
     */
    public static function prepare(RequestData $requestData)
    {
        $requestType = new VersendenApi\Version(
            $requestData->getMajorRelease(),
            $requestData->getMinorRelease(),
            $requestData->getBuild()
        );

        return $requestType;
    }
}
