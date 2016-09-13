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
 * @package   Dhl\Versenden\Info
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
namespace Dhl\Versenden\Info;
use Dhl\Versenden\Info;

/**
 * Receiver
 *
 * @category Dhl
 * @package  Dhl\Versenden\Info
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Receiver extends AbstractInfo
{
    /** @var Receiver\Packstation */
    private $packstation;
    /** @var Receiver\Postfiliale */
    private $postfiliale;
    /** @var Receiver\ParcelShop */
    private $parcelShop;

    /**
     * @param \stdClass $object
     * @return Receiver|null
     */
    public static function fromObject(\stdClass $object)
    {
        //TODO(nr): implement
        $receiver = new self();
        return $receiver;
    }
}
