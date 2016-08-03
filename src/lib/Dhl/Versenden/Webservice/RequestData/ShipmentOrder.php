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
use Dhl\Versenden\Config\Shipper;
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

    const PRODUCT_CODE_PAKET_NATIONAL  = 'V01PAK';
    const PRODUCT_CODE_WELTPAKET = 'V53WPAK';
    const PRODUCT_CODE_EUROPAKET = 'V54EPAK';
    const PRODUCT_CODE_KURIER_TAGGLEICH = 'V06TG';
    const PRODUCT_CODE_KURIER_WUNSCHZEIT = 'V06WZ';
    const PRODUCT_CODE_PAKET_AUSTRIA = 'V86PARCEL';
    const PRODUCT_CODE_PAKET_CONNECT = 'V87PARCEL';
    const PRODUCT_CODE_PAKET_INTERNATIONAL = 'V82PARCEL';

    /** @var ShipmentOrder\Shipper */
    private $shipper;
    /** @var ShipmentOrder\GlobalSettings */
    private $globalSettings;
    /** @var Receiver */
    private $receiver;
    /** @var ShipmentSettings */
    private $shipmentSettings;
    /** @var ServiceSettings */
    private $serviceSettings;
    /** @var string */
    private $accountNumber;
    /** @var bool */
    private $printOnlyIfCodable;
    /** @var string */
    private $labelResponseType;
    /** @var string */
    private $productCode;
    /** @var string */
    private $sequenceNumber;

    /**
     * ShipmentOrder constructor.
     * @param ShipmentOrder\Shipper $shipper
     * @param ShipmentOrder\Receiver $receiver
     * @param ShipmentOrder\Settings\GlobalSettings $globalSettings
     * @param ShipmentOrder\Settings\ShipmentSettings $shipmentSettings
     * @param ShipmentOrder\Settings\ServiceSettings $serviceSettings
     * @param int $sequenceNumber
     * @param string $labelType
     */
    public function __construct(
        ShipmentOrder\Shipper $shipper,
        ShipmentOrder\Receiver $receiver,
        ShipmentOrder\Settings\GlobalSettings $globalSettings,
        ShipmentOrder\Settings\ShipmentSettings $shipmentSettings,
        ShipmentOrder\Settings\ServiceSettings $serviceSettings,
        $sequenceNumber = 1,
        $labelType = self::LABEL_TYPE_B64
    ) {
        $this->shipmentSettings = $shipmentSettings;
        $this->shipper = $shipper;
        $this->receiver = $receiver;

        $this->globalSettings = $globalSettings;
        $this->serviceSettings = $serviceSettings;

        $this->accountNumber = sprintf(
            '%s%s%s',
            $shipper->getAccount()->getEkp(),
            preg_filter('/[^\d]/', '', $shipmentSettings->getProduct()),
            $shipper->getAccount()->getParticipationDefault()
        );

        $this->printOnlyIfCodable = $globalSettings->isPrintOnlyIfCodable();
        $this->labelResponseType = $labelType;

        $this->productCode = $shipmentSettings->getProduct();
        $this->sequenceNumber = $sequenceNumber;
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
     * @return ShipmentOrder\Settings\GlobalSettings
     */
    public function getGlobalSettings()
    {
        return $this->globalSettings;
    }

    /**
     * @return ShipmentOrder\Settings\ShipmentSettings
     */
    public function getShipmentSettings()
    {
        return $this->shipmentSettings;
    }

    /**
     * @return ShipmentOrder\Settings\ServiceSettings
     */
    public function getServiceSettings()
    {
        return $this->serviceSettings;
    }

    /**
     * @return string
     */
    public function getAccountNumber()
    {
        return $this->accountNumber;
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
    public function getSequenceNumber()
    {
        return $this->sequenceNumber;
    }
}
