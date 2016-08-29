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
 * @package   Dhl\Versenden\Webservice\ResponseData
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
namespace Dhl\Versenden\Webservice\ResponseData;
/**
 * CreateShipment
 *
 * @category Dhl
 * @package  Dhl\Versenden\Webservice\ResponseData
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
final class CreateShipment
{
    /** @var Status */
    private $status;
    /** @var LabelCollection */
    private $labels;
    /** @var string[] */
    private $shipmentNumbers;

    /**
     * CreateShipment constructor.
     * @param Status $status
     * @param LabelCollection $labels
     */
    public function __construct(Status $status, LabelCollection $labels, array $shipmentNumbers)
    {
        $this->status          = $status;
        $this->labels          = $labels;
        $this->shipmentNumbers = $shipmentNumbers;
    }

    /**
     * @return Status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return LabelCollection
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * Obtain sequence number to shipment number mapping.
     *
     * @return \string[]
     */
    public function getShipmentNumbers()
    {
        return $this->shipmentNumbers;
    }

    /**
     * Obtain created shipment number by given sequence number
     *
     * @param string $sequenceNumber
     * @return null|string
     */
    public function getShipmentNumber($sequenceNumber)
    {
        $numbers = $this->getShipmentNumbers();
        if (!isset($numbers[$sequenceNumber])) {
            return null;
        }

        return $numbers[$sequenceNumber];
    }
}
