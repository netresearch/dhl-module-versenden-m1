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
use \Dhl\Versenden\Bcs\Api\Webservice\RequestData;
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
    /** @var Dhl_Versenden_Model_Webservice_Builder_Customs */
    protected $customsBuilder;
    /** @var Dhl_Versenden_Model_Webservice_Builder_Settings */
    protected $settingsBuilder;
    /** @var Dhl_Versenden_Model_Info_Builder */
    protected $infoBuilder;

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
            'customs_builder'  => Dhl_Versenden_Model_Webservice_Builder_Customs::class,
            'settings_builder' => Dhl_Versenden_Model_Webservice_Builder_Settings::class,
            'info_builder'     => Dhl_Versenden_Model_Info_Builder::class
        );

        $missingArguments = array_diff_key($argDef, $args);
        if (!empty($missingArguments)) {
            $message = sprintf('required arguments missing: %s', implode(', ', array_keys($missingArguments)));
            throw new Mage_Core_Exception($message);
        }

        $invalidArgumentFilter = function ($key) use ($args, $argDef) {
            return !$args[$key] instanceof $argDef[$key];
        };
        $invalidArguments = array_filter(array_keys($argDef), $invalidArgumentFilter);

        if (!empty($invalidArguments)) {
            $message = sprintf('invalid arguments: %s', implode(', ', $invalidArguments));
            throw new Mage_Core_Exception($message);
        }

        $this->shipperBuilder = $args['shipper_builder'];
        $this->receiverBuilder = $args['receiver_builder'];
        $this->serviceBuilder = $args['service_builder'];
        $this->packageBuilder = $args['package_builder'];
        $this->customsBuilder = $args['customs_builder'];
        $this->settingsBuilder = $args['settings_builder'];
        $this->infoBuilder = $args['info_builder'];

        return $this;
    }

    /**
     * @param int $sequenceNumber
     * @param string $shipmentDate
     * @param Mage_Sales_Model_Order_Shipment $shipment
     * @param string[] $packageInfo
     * @param string[] $serviceInfo
     * @param string[] $customsInfo
     * @param string $gkApiProduct
     * @return RequestData\ShipmentOrder
     */
    public function getShipmentOrder(
        $sequenceNumber,
        $shipmentDate,
        Mage_Sales_Model_Order_Shipment $shipment,
        array $packageInfo,
        array $serviceInfo,
        array $customsInfo,
        $gkApiProduct
    )
    {
        $shipper = $this->shipperBuilder->getShipper($shipment->getStoreId());

        $versendenInfo = $shipment->getShippingAddress()->getData('dhl_versenden_info');

        if (!$versendenInfo instanceof \Dhl\Versenden\Bcs\Api\Info) {
            // build receiver from shipping address
            $receiver = $this->receiverBuilder->getReceiver($shipment->getShippingAddress());
        } else {
            // read receiver from prepared address (split, eventually updated)
            $versendenReceiver = $versendenInfo->getReceiver();
            $packstation = isset($versendenReceiver->getPackstation()->packstationNumber)
                ? new RequestData\ShipmentOrder\Receiver\Packstation(
                    $versendenReceiver->getPackstation()->zip,
                    $versendenReceiver->getPackstation()->city,
                    $versendenReceiver->getPackstation()->country,
                    $versendenReceiver->getPackstation()->countryISOCode,
                    $versendenReceiver->getPackstation()->state,
                    $versendenReceiver->getPackstation()->packstationNumber,
                    $versendenReceiver->getPackstation()->postNumber)
                : null;
            $postfiliale = isset($versendenReceiver->getPostfiliale()->postfilialNumber)
                ? new RequestData\ShipmentOrder\Receiver\Postfiliale(
                    $versendenReceiver->getPostfiliale()->zip,
                    $versendenReceiver->getPostfiliale()->city,
                    $versendenReceiver->getPostfiliale()->country,
                    $versendenReceiver->getPostfiliale()->countryISOCode,
                    $versendenReceiver->getPostfiliale()->state,
                    $versendenReceiver->getPostfiliale()->postfilialNumber,
                    $versendenReceiver->getPostfiliale()->postNumber)
                : null;
            $parcelShop = isset($versendenReceiver->getParcelShop()->parcelShopNumber)
                ? new RequestData\ShipmentOrder\Receiver\ParcelShop(
                    $versendenReceiver->getParcelShop()->zip,
                    $versendenReceiver->getParcelShop()->city,
                    $versendenReceiver->getParcelShop()->country,
                    $versendenReceiver->getParcelShop()->countryISOCode,
                    $versendenReceiver->getParcelShop()->state,
                    $versendenReceiver->getParcelShop()->parcelShopNumber,
                    $versendenReceiver->getParcelShop()->streetName,
                    $versendenReceiver->getParcelShop()->streetNumber)
                : null;

            $receiver = new RequestData\ShipmentOrder\Receiver(
                $versendenReceiver->name1,
                $versendenReceiver->name2,
                $versendenReceiver->name3,
                $versendenReceiver->streetName,
                $versendenReceiver->streetNumber,
                $versendenReceiver->addressAddition,
                $versendenReceiver->dispatchingInformation,
                $versendenReceiver->zip,
                $versendenReceiver->city,
                $versendenReceiver->country,
                $versendenReceiver->countryISOCode,
                $versendenReceiver->state,
                $versendenReceiver->phone,
                $versendenReceiver->email,
                $versendenReceiver->contactPerson,
                $packstation,
                $postfiliale,
                $parcelShop
            );
        }

        $serviceSelection = $this->serviceBuilder->getServiceSelection($shipment->getOrder(), $serviceInfo);

        $packages = $this->packageBuilder->getPackages($packageInfo);

        /** @var Mage_Sales_Model_Resource_Order_Invoice_Collection $invoiceCollection */
        $invoiceCollection = $shipment->getOrder()->getInvoiceCollection();
        /** @var Mage_Sales_Model_Order_Invoice $invoice */
        $invoice = $invoiceCollection->getFirstItem();

        $exportDocuments = $this->customsBuilder->getExportDocuments(
            $invoice->getIncrementId(),
            $customsInfo,
            $packageInfo
        );

        $globalSettings = $this->settingsBuilder->getSettings($shipment->getStoreId());

        $shipmentOrder = new RequestData\ShipmentOrder(
            $sequenceNumber,
            $shipment->getOrder()->getIncrementId(),
            $shipper,
            $receiver,
            $serviceSelection,
            $packages,
            $exportDocuments,
            $gkApiProduct,
            $shipmentDate,
            $globalSettings->getLabelType()
        );

        // update dhl_versenden_info with current address and service selection
        $versendenInfo = $this->infoBuilder->infoFromRequestData($shipmentOrder);
        $shipment->getShippingAddress()->setData('dhl_versenden_info', $versendenInfo);

        return $shipmentOrder;
    }
}
