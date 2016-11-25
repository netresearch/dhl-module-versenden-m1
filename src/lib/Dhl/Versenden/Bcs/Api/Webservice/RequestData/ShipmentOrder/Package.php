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
 * @package   Dhl\Versenden\Bcs\Api\Webservice\RequestData
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
namespace Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder;
use Dhl\Versenden\Bcs\Api\Webservice\RequestData;

/**
 * Package
 *
 * @category Dhl
 * @package  Dhl\Versenden\Bcs\Api\Webservice\RequestData
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Package extends RequestData
{
    /** @var int */
    private $packageId;
    /** @var float */
    private $weightInKG;
    /** @var int */
    private $lengthInCM;
    /** @var int */
    private $widthInCM;
    /** @var int */
    private $heightInCM;

    /**
     * Package constructor.
     * @param int $packageId
     * @param float $weightInKG
     * @param null $lengthInCM
     * @param null $widthInCM
     * @param null $heightInCM
     */
    public function __construct($packageId, $weightInKG, $lengthInCM = null, $widthInCM = null, $heightInCM = null)
    {
        $this->packageId  = $packageId;
        $this->weightInKG = $weightInKG;
        $this->lengthInCM = $lengthInCM;
        $this->widthInCM  = $widthInCM;
        $this->heightInCM = $heightInCM;
    }

    /**
     * @return int
     */
    public function getPackageId()
    {
        return $this->packageId;
    }

    /**
     * @return float
     */
    public function getWeightInKG()
    {
        return $this->weightInKG;
    }

    /**
     * @return int
     */
    public function getLengthInCM()
    {
        return $this->lengthInCM;
    }

    /**
     * @return int
     */
    public function getWidthInCM()
    {
        return $this->widthInCM;
    }

    /**
     * @return int
     */
    public function getHeightInCM()
    {
        return $this->heightInCM;
    }
}
