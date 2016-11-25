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

/**
 * Services
 *
 * @category Dhl
 * @package  Dhl\Versenden\Bcs\Api\Info
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Services extends ArrayableInfo
{
    /** @var bool|string false or date */
    public $preferredDay;
    /** @var bool|string false or time */
    public $preferredTime;
    /** @var bool|string false or location */
    public $preferredLocation;
    /** @var bool|string false or neighbour address */
    public $preferredNeighbour;
    /** @var bool false or true */
    public $parcelAnnouncement;
    /** @var bool|string false or A16 or A18 */
    public $visualCheckOfAge;
    /** @var bool false or true */
    public $returnShipment;
    /** @var bool|float false or amount */
    public $insurance;
    /** @var bool false or true */
    public $bulkyGoods;
    /** @var bool|float false or amount */
    public $cod;
    /** @var bool false or true */
    public $printOnlyIfCodeable;
}
