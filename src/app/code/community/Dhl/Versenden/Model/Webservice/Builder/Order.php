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
 * @package   Dhl_Versenden
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
use \Dhl\Versenden\Webservice\RequestData;
/**
 * Dhl_Versenden_Model_Webservice_Builder_Order
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Model_Webservice_Builder_Order
{
    /** @var Dhl_Versenden_Model_Webservice_Builder_Shipper */
    protected $shipperBuilder;
    /** @var Dhl_Versenden_Model_Webservice_Builder_Receiver */
    protected $receiverBuilder;
    /** @var Dhl_Versenden_Model_Webservice_Builder_Service */
    protected $serviceBuilder;
    /** @var Dhl_Versenden_Model_Webservice_Builder_Package */
    protected $packageBuilder;
    /** @var Dhl_Versenden_Model_Webservice_Builder_Settings */
    protected $settingsBuilder;

    /**
     * Dhl_Versenden_Model_Webservice_Builder_Order constructor.
     *
     * @param mixed[] $args
     * @throws Mage_Core_Exception
     */
    public function __construct($args)
    {
        $argDef = array(
            'shipper_builder'  => Dhl_Versenden_Model_Webservice_Builder_Shipper::class,
            'receiver_builder' => Dhl_Versenden_Model_Webservice_Builder_Receiver::class,
            'service_builder'  => Dhl_Versenden_Model_Webservice_Builder_Service::class,
            'package_builder'  => Dhl_Versenden_Model_Webservice_Builder_Package::class,
            'settings_builder' => Dhl_Versenden_Model_Webservice_Builder_Settings::class
        );

        $missingArguments = array_diff_key($argDef, $args);
        if (count($missingArguments)) {
            $message = sprintf('required arguments missing: %s', implode(', ', array_keys($missingArguments)));
            throw new Mage_Core_Exception($message);
        }

        $invalidArgumentFilter = function ($key) use ($args, $argDef) {
            return !$args[$key] instanceof $argDef[$key];
        };
        $invalidArguments = array_filter(array_keys($argDef), $invalidArgumentFilter);

        if (count($invalidArguments)) {
            $message = sprintf('invalid arguments: %s', implode(', ', $invalidArguments));
            throw new Mage_Core_Exception($message);
        }

        $this->packageBuilder = $args['package_builder'];
        $this->serviceBuilder = $args['service_builder'];
        $this->shipperBuilder = $args['shipper_builder'];
        $this->settingsBuilder = $args['settings_builder'];
        $this->receiverBuilder = $args['receiver_builder'];

        return $this;
    }

    /**
     * @param int $sequenceNumber
     * @param string $shipmentDate
     * @param Mage_Sales_Model_Order_Shipment $shipment
     * @param string[] $packageInfo
     * @param string[] $serviceInfo
     * @return RequestData\ShipmentOrder
     */
    public function getShipmentOrder(
        $sequenceNumber,
        $shipmentDate,
        Mage_Sales_Model_Order_Shipment $shipment,
        array $packageInfo,
        array $serviceInfo
    )
    {
        $shipper = $this->shipperBuilder->getShipper($shipment->getStoreId());

        $shippingInfoJson = $shipment->getShippingAddress()->getData('dhl_versenden_info');
        $shippingInfoObj = json_decode($shippingInfoJson);
        $shippingInfo = RequestData\ObjectMapper::getShippingInfo((object)$shippingInfoObj);
        if (!$shippingInfo) {
            $receiver = $this->receiverBuilder->getReceiver($shipment->getShippingAddress());
        } else {
            $receiver = $shippingInfo->getReceiver();
        }

        $serviceSelection = $this->serviceBuilder->getServiceSelection($shipment->getOrder(), $serviceInfo);

        $packageCollection = $this->packageBuilder->getPackages($packageInfo);

        $package = current($packageInfo);
        $productCode = $package['params']['container'];

        $globalSettings = $this->settingsBuilder->getSettings($shipment->getStoreId());


        return new RequestData\ShipmentOrder(
            $sequenceNumber,
            $shipment->getOrder()->getIncrementId(),
            $shipper,
            $receiver,
            $serviceSelection,
            $packageCollection,
            $productCode,
            $shipmentDate,
            $globalSettings->isPrintOnlyIfCodable(),
            $globalSettings->getLabelType()
        );
    }
}
