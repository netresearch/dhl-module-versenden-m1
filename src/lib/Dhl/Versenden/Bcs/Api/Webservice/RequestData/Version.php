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
namespace Dhl\Versenden\Bcs\Api\Webservice\RequestData;
use Dhl\Versenden\Bcs\Api\Webservice\RequestData;

/**
 * Version
 *
 * @category Dhl
 * @package  Dhl\Versenden\Bcs\Api\Webservice\RequestData
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Version extends RequestData
{
    /** @var string */
    private $majorRelease;
    /** @var string */
    private $minorRelease;
    /** @var string */
    private $build;

    /**
     * Version constructor.
     * @param string $majorRelease
     * @param string $minorRelease
     * @param string $build
     */
    public function __construct($majorRelease, $minorRelease, $build = null)
    {
        $this->majorRelease = $majorRelease;
        $this->minorRelease = $minorRelease;
        $this->build = $build;
    }

    /**
     * @return string
     */
    public function getMajorRelease()
    {
        return $this->majorRelease;
    }

    /**
     * @return string
     */
    public function getMinorRelease()
    {
        return $this->minorRelease;
    }

    /**
     * @return string
     */
    public function getBuild()
    {
        return $this->build;
    }
}
