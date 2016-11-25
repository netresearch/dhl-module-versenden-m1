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
 * @package   Dhl\Versenden\Bcs\Api\Info
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
namespace Dhl\Versenden\Bcs\Api\Info;
use Dhl\Versenden\Bcs\Api\Info;

/**
 * Serializer
 *
 * @category Dhl
 * @package  Dhl\Versenden\Bcs\Api\Info
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Serializer
{
    /**
     * @param Info $info
     * @return string
     */
    public static function serialize(Info $info)
    {
        return json_encode($info, JSON_FORCE_OBJECT);
    }

    /**
     * @param $serialized
     * @return Info|null
     */
    public static function unserialize($serialized)
    {
        return Info::fromJson($serialized);
    }
}
