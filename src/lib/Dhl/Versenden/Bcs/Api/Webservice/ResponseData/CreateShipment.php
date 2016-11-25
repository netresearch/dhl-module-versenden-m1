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
 * @package   Dhl\Versenden\Bcs\Api\Webservice\ResponseData
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
namespace Dhl\Versenden\Bcs\Api\Webservice\ResponseData;
use \Dhl\Versenden\Bcs\Api\Webservice\ResponseData\Status\Response as ResponseStatus;
use Dhl\Versenden\Bcs\Api\Webservice\ResponseData\CreateShipment\LabelCollection;
/**
 * CreateShipment
 *
 * @category Dhl
 * @package  Dhl\Versenden\Bcs\Api\Webservice\ResponseData
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class CreateShipment
{
    /** @var ResponseStatus */
    private $status;
    /** @var LabelCollection */
    private $createdItems;
    /** @var string[] */
    private $shipmentNumbers;

    /**
     * CreateShipment constructor.
     * @param ResponseStatus $status
     * @param LabelCollection $labels
     * @param string[] $shipmentNumbers
     */
    public function __construct(ResponseStatus $status, LabelCollection $labels, array $shipmentNumbers)
    {
        $this->status          = $status;
        $this->createdItems    = $labels;
        $this->shipmentNumbers = $shipmentNumbers;
    }

    /**
     * @return ResponseStatus
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return LabelCollection
     */
    public function getCreatedItems()
    {
        return $this->createdItems;
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
