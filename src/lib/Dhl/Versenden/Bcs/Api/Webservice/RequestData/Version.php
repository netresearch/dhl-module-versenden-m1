<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Webservice\RequestData;

use Dhl\Versenden\Bcs\Api\Webservice\RequestData;

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
