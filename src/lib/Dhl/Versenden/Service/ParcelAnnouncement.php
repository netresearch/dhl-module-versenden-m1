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
 * @package   Dhl\Versenden\Service
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
namespace Dhl\Versenden\Service;
use Dhl\Versenden\Service as AbstractService;

/**
 * ParcelAnnouncement
 *
 * @category Dhl
 * @package  Dhl\Versenden\Service
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class ParcelAnnouncement extends AbstractService
{
    /**
     * ParcelAnnouncement constructor.
     * @param string $defaultValue
     */
    public function __construct($defaultValue = '')
    {
        parent::__construct($defaultValue);

        $this->name = 'Parcel Announcement';
        $this->isCustomerService = true;
        $this->frontendInputType = self::INPUT_TYPE_BOOLEAN;
    }

    /**
     * Let the user decide whether to enable this service or not.
     */
    public function setIsOptional()
    {
        $this->frontendInputType = self::INPUT_TYPE_BOOLEAN;
        $this->defaultValue = false;
    }

    /**
     * Always enable this service.
     */
    public function setIsRequired()
    {
        $this->frontendInputType = self::INPUT_TYPE_HIDDEN;
        $this->defaultValue = true;
    }
}
