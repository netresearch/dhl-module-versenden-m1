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
use Dhl\Versenden\Config\Shipment\Settings;
use Dhl\Versenden\Config\Shipper;
use Dhl\Versenden\ShippingInfo\Receiver;

/**
 * ShipmentOrder
 *
 * @category Dhl
 * @package  Dhl\Versenden\Webservice\RequestData
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class ShipmentOrder
{
    /** @var bool */
    private $printOnlyIfCodable;
    /** @var string */
    private $labelResponseType;
    /** @var null */
    private $shipmentDetails;
    /** @var Shipper */
    private $shipper;
    /** @var Receiver */
    private $receiver;

    /**
     * ShipmentOrder constructor.
     * @param Shipper $shipper
     * @param Receiver $receiver
     * @param Settings $settings
     */
    public function __construct(Shipper $shipper, Receiver $receiver, Settings $settings)
    {
        $this->printOnlyIfCodable = $settings->printOnlyIfCodable;
        $this->labelResponseType = 'B64';
        $this->shipmentDetails = null;
        $this->shipper = $shipper;
        $this->receiver = $receiver;
    }
}
