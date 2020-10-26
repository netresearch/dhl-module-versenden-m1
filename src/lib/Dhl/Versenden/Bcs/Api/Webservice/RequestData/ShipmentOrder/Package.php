<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder;

use Dhl\Versenden\Bcs\Api\Webservice\RequestData;

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
