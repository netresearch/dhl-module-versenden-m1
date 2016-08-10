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
 * @package   Dhl\Versenden\Webservice\RequestData
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
namespace Dhl\Versenden\Webservice\RequestData\ShipmentOrder;
use Dhl\Versenden\Webservice\RequestData;

/**
 * Package
 *
 * @category Dhl
 * @package  Dhl\Versenden\Webservice\RequestData
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Package extends RequestData implements \JsonSerializable
{
    /** @var int */
    private $sequenceNumber;
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
     * @param $sequenceNumber
     * @param $weightInKG
     * @param null $lengthInCM
     * @param null $widthInCM
     * @param null $heightInCM
     */
    public function __construct($sequenceNumber,
        $weightInKG, $lengthInCM = null, $widthInCM = null, $heightInCM = null
    ) {
        $this->sequenceNumber = $sequenceNumber;
        $this->weightInKG = $weightInKG;
        $this->lengthInCM = $lengthInCM;
        $this->widthInCM  = $widthInCM;
        $this->heightInCM = $heightInCM;
    }

    /**
     * @return int
     */
    public function getSequenceNumber()
    {
        return $this->sequenceNumber;
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

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
