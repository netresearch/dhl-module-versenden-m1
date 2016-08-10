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
namespace Dhl\Versenden\Webservice\RequestData;
use Dhl\Versenden\Webservice\RequestData;

/**
 * ShipmentOrder
 *
 * This class holds everything needed for creating a shipment order at DHL:
 * - general
 *   -- billing number (EKP + Procedure + Participation)
 *   -- return billing number (EKP + Procedure + Participation)
 *   -- printOnlyIfCodable flag
 *   -- labelResponseType
 *   -- product code
 * - shipper
 *   -- address
 *   -- bank data
 *   -- return receiver
 * - receiver
 *   -- address
 * - order / shipment
 *   -- sales order data (weight, increment id)
 *   -- export document
 *   -- shipment date
 * - services
 *   -- selectable
 *   -- implicit (cod status, parcel announcement)
 * It is supposed to be independent of the services that processes the shipment order.
 *
 * @category Dhl
 * @package  Dhl\Versenden\Webservice\RequestData
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class ShipmentOrder extends RequestData
{
    const LABEL_TYPE_B64 = 'B64';
    const LABEL_TYPE_URL = 'URL';

    /** @var string */
    private $sequenceNumber;
    /** @var string */
    private $reference;
    /** @var ShipmentOrder\Shipper */
    private $shipper;
    /** @var ShipmentOrder\Receiver */
    private $receiver;
    /** @var ShipmentOrder\ServiceSelection */
    private $serviceSelection;
    /** @var ShipmentOrder\PackageCollection */
    private $packages;
    /** @var string */
    private $productCode;
    /** @var string */
    private $shipmentDate;
    /** @var string */
    private $accountNumber;
    /** @var bool */
    private $printOnlyIfCodable;
    /** @var string */
    private $labelResponseType;

    /**
     * ShipmentOrder constructor.
     *
     * @param int                             $sequenceNumber
     * @param string                          $reference
     * @param ShipmentOrder\Shipper           $shipper
     * @param ShipmentOrder\Receiver          $receiver
     * @param ShipmentOrder\ServiceSelection  $serviceSelection
     * @param ShipmentOrder\PackageCollection $packages
     * @param string                          $productCode
     * @param string                          $shipmentDate
     * @param bool                            $printOnlyIfCodable
     * @param string                          $labelType
     */
    public function __construct(
        $sequenceNumber,
        $reference,
        ShipmentOrder\Shipper $shipper,
        ShipmentOrder\Receiver $receiver,
        ShipmentOrder\ServiceSelection $serviceSelection,
        ShipmentOrder\PackageCollection $packages,
        $productCode,
        $shipmentDate,
        $printOnlyIfCodable,
        $labelType = self::LABEL_TYPE_B64
    ) {
        $this->sequenceNumber = $sequenceNumber;
        $this->reference = $reference;

        $this->packages = $packages;
        $this->shipper = $shipper;
        $this->receiver = $receiver;

        $this->serviceSelection = $serviceSelection;

        $this->accountNumber = sprintf(
            '%s%s%s',
            $shipper->getAccount()->getEkp(),
            preg_filter('/[^\d]/', '', $productCode),
            $shipper->getAccount()->getParticipationDefault()
        );

        $this->productCode        = $productCode;
        $this->shipmentDate       = $shipmentDate;
        $this->printOnlyIfCodable = $printOnlyIfCodable;
        $this->labelResponseType  = $labelType;
    }

    /**
     * @return string
     */
    public function getSequenceNumber()
    {
        return $this->sequenceNumber;
    }

    /**
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @return ShipmentOrder\Shipper
     */
    public function getShipper()
    {
        return $this->shipper;
    }

    /**
     * @return ShipmentOrder\Receiver
     */
    public function getReceiver()
    {
        return $this->receiver;
    }

    /**
     * @return ShipmentOrder\ServiceSelection
     */
    public function getServiceSelection()
    {
        return $this->serviceSelection;
    }

    /**
     * @return ShipmentOrder\PackageCollection
     */
    public function getPackages()
    {
        return $this->packages;
    }

    /**
     * @return string
     */
    public function getAccountNumber()
    {
        return $this->accountNumber;
    }

    /**
     * @return string
     */
    public function getProductCode()
    {
        return $this->productCode;
    }

    /**
     * @return string
     */
    public function getShipmentDate()
    {
        return $this->shipmentDate;
    }

    /**
     * @return boolean
     */
    public function isPrintOnlyIfCodable()
    {
        return $this->printOnlyIfCodable;
    }

    /**
     * @return string
     */
    public function getLabelResponseType()
    {
        return $this->labelResponseType;
    }
}
