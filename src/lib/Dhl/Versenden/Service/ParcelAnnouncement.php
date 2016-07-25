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
    const DISPLAY_MODE_REQUIRED = 1;
    const DISPLAY_MODE_OPTIONAL = 2;

    /** @var string */
    public $frontendInputType = self::INPUT_TYPE_BOOLEAN;

    /**
     * ParcelAnnouncement constructor.
     * @param string $value
     */
    public function __construct($value = '')
    {
        parent::__construct($value);

        $this->name = 'Parcel Announcement';
        $this->isCustomerService = true;

        $this->setDisplayMode($value);
    }

    /**
     * Let the user decide whether to enable this service or not.
     */
    public function setIsOptional()
    {
        $this->frontendInputType = self::INPUT_TYPE_BOOLEAN;
    }

    /**
     * Always enable this service.
     */
    public function setIsRequired()
    {
        $this->frontendInputType = self::INPUT_TYPE_HIDDEN;
    }

    /**
     * Set whether to display/render this service or not.
     *
     * @param int $displayMode
     */
    public function setDisplayMode($displayMode)
    {
        switch ($displayMode) {
            case self::DISPLAY_MODE_OPTIONAL:
                $this->setIsOptional();
                break;
            case self::DISPLAY_MODE_REQUIRED:
                $this->setIsRequired();
                break;
            default:
        }
    }
}
