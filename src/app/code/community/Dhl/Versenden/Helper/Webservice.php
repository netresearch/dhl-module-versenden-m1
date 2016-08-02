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
use \Dhl\Versenden\Webservice;
use \Dhl\Versenden\Webservice\RequestData\ShipmentOrder;
use \Dhl\Bcs\Api as VersendenApi;
/**
 * Dhl_Versenden_Helper_Webservice
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Helper_Webservice extends Dhl_Versenden_Helper_Data
{
    const ADAPTER_TYPE_SOAP = 'soap';

    const OPERATION_GET_VERSION = 'getVersion';
    const OPERATION_CREATE_SHIPMENT_ORDER = 'createShipmentOrder';
    const OPERATION_DELETE_SHIPMENT_ORDER = 'deleteShipmentOrder';
    const OPERATION_GET_LABEL = 'getLabel';
    const OPERATION_EXPORT_DOC = 'getExportDoc';
    const OPERATION_DO_MANIFEST = 'doManifest';
    const OPERATION_GET_MANIFEST = 'getManifest';
    const OPERATION_UPDATE_SHIPMENT_ORDER = 'updateShipmentOrder';
    const OPERATION_VALIDATE_SHIPMENT = 'validateShipment';

    /**
     * @param string $operation
     * @return Webservice\Parser
     */
    protected function getSoapParser($operation)
    {
        switch ($operation) {
            case self::OPERATION_GET_VERSION:
                return new Webservice\Parser\Soap\Version();
            case self::OPERATION_CREATE_SHIPMENT_ORDER:
                return new Webservice\Parser\Soap\CreateShipmentOrder();
            default:
                return null;
        }
    }

    /**
     * @return Webservice\Adapter\Soap
     */
    protected function getSoapAdapter(Dhl_Versenden_Model_Config_Shipper $config)
    {
        $options = array(
            'location' => $config->getEndpoint(),
            'login' => $config->getWebserviceAuthUsername(),
            'password' => $config->getWebserviceAuthPassword(),
        );
        $client = new \Dhl\Bcs\Api\GVAPI_2_0_de($options);

        $authHeader = new \SoapHeader(
            'http://dhl.de/webservice/cisbase',
            'Authentification',
            array(
                'user' => $config->getAccountSettings()->getUser(),
                'signature' => $config->getAccountSettings()->getSignature(),
            )
        );
        $client->__setSoapHeaders($authHeader);

        return new Webservice\Adapter\Soap($client);
    }

    /**
     * @param string $type
     * @return Webservice\Adapter
     */
    public function getWebserviceAdapter($type)
    {
        $adapter = null;
        $config = Mage::getModel('dhl_versenden/config_shipper');

        switch ($type) {
            case self::ADAPTER_TYPE_SOAP:
            default:
                $adapter = $this->getSoapAdapter($config);
        }

        return $adapter;
    }

    /**
     * @param string $type
     * @param string $operation
     * @return Webservice\Parser
     */
    public function getWebserviceParser($type, $operation)
    {
        $parser = null;

        switch ($type) {
            case self::ADAPTER_TYPE_SOAP:
            default:
                $parser = $this->getSoapParser($operation);
        }

        return $parser;
    }

    /**
     * Create one shipment order from given shipment
     *
     * @param Mage_Sales_Model_Order_Shipment $shipment
     * @param int $sequenceNumber
     * @param array $orderData
     * @return ShipmentOrder
     */
    public function shipmentToShipmentOrder(
        Mage_Sales_Model_Order_Shipment $shipment, $sequenceNumber = 1, $orderData = array()
    ) {
        // (1) data derived from config
        $shipperConfig = Mage::getModel('dhl_versenden/config_shipper');
        $shipmentConfig = Mage::getModel('dhl_versenden/config_shipment');

        $shipper        = $shipperConfig->getShipper($shipment->getStoreId());
        /** @var ShipmentOrder\Settings\GlobalSettings $globalSettings */
        $globalSettings = $shipmentConfig->getSettings($shipment->getStoreId());


        // (2) data derived from OPC
        $shippingInfoJson = $shipment->getShippingAddress()->getDhlVersendenInfo();
        $shippingInfoObj = json_decode($shippingInfoJson);
        $shippingInfo = Webservice\RequestData\ObjectMapper::getShippingInfo($shippingInfoObj);

        $receiver = $shippingInfo->getReceiver();
        $serviceSettings = $shippingInfo->getServiceSettings();


        // (3) data derived from shipment creation
        // add/override shipment and service settings from shipment creation
        $weight = $this->calculateItemsWeight(
            $shipment->getAllItems(),
            $globalSettings->getProductWeight(),
            $globalSettings->getUnitOfMeasure()
        );
        $shipmentSettings = new ShipmentOrder\Settings\ShipmentSettings(
            $this->utcToCet(null, 'Y-m-d'),
            $shipment->getIncrementId(),
            $weight,
            $orderData['product']
        );

        return new Webservice\RequestData\ShipmentOrder(
            $shipper,
            $receiver,
            $globalSettings,
            $shipmentSettings,
            $serviceSettings,
            $sequenceNumber,
            $globalSettings->getLabelType()
        );
    }

    /**
     * Create receiver from given shipping address
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return ShipmentOrder\Receiver
     */
    public function shippingAddressToReceiver(Mage_Sales_Model_Quote_Address $address)
    {
        $countryDirectory = Mage::getModel('directory/country')->loadByCode($address->getCountry());
        $country        = $countryDirectory->getName();
        $countryISOCode = $countryDirectory->getIso2Code();

        $street = $this->splitStreet($address->getStreetFull());
        $streetName      = $street['street_name'];
        $streetNumber    = $street['street_number'];
        $addressAddition = $street['supplement'];


        // let 3rd party extensions add postal facility data
        $facility = new Varien_Object();
        Mage::dispatchEvent('dhl_versenden_set_postal_facility', array(
            'quote_address' => $address,
            'postal_facility' => $facility,
        ));

        $packStation = null;
        if ($facility->getData('shop_type') === ShipmentOrder\Receiver\PostalFacility::TYPE_PACKSTATION) {
            $packStation = new ShipmentOrder\Receiver\Packstation(
                $address->getPostcode(),
                $address->getCity(),
                $country,
                $countryISOCode,
                $address->getRegion(),
                $facility->getData('shop_number'),
                $facility->getData('post_number')
            );
        }

        $postFiliale = null;
        if ($facility->getData('shop_type') === ShipmentOrder\Receiver\PostalFacility::TYPE_POSTFILIALE) {
            $postFiliale = new ShipmentOrder\Receiver\Postfiliale(
                $address->getPostcode(),
                $address->getCity(),
                $country,
                $countryISOCode,
                $address->getRegion(),
                $facility->getData('shop_number'),
                $facility->getData('post_number')
            );
        }

        $parcelShop = null;
        if ($facility->getData('shop_type') === ShipmentOrder\Receiver\PostalFacility::TYPE_PAKETSHOP) {
            $parcelShop = new ShipmentOrder\Receiver\ParcelShop(
                $address->getPostcode(),
                $address->getCity(),
                $country,
                $countryISOCode,
                $address->getRegion(),
                $facility->getData('shop_number'),
                $streetName,
                $streetNumber
            );
        }

        return new Dhl\Versenden\Webservice\RequestData\ShipmentOrder\Receiver(
            $address->getName(),
            $address->getCompany(),
            '',
            $streetName,
            $streetNumber,
            $addressAddition,
            '',
            $address->getPostcode(),
            $address->getCity(),
            $country,
            $countryISOCode,
            $address->getRegion(),
            $address->getTelephone(),
            $address->getEmail(),
            '',
            $packStation,
            $postFiliale,
            $parcelShop
        );
    }

    /**
     * @param bool[] $selectedServices
     * @param string[] $serviceDetails
     * @return ShipmentOrder\Settings\ServiceSettings
     */
    public function serviceSelectionToServiceSettings(array $selectedServices, array $serviceDetails)
    {
        $dayOfDelivery = false;
        if (isset($selectedServices['dayOfDelivery']) && isset($serviceDetails['dayOfDelivery'])) {
            $dayOfDelivery = $serviceDetails['dayOfDelivery'];
        }

        $deliveryTimeFrame = false;
        if (isset($selectedServices['deliveryTimeFrame']) && isset($serviceDetails['deliveryTimeFrame'])) {
            $deliveryTimeFrame = $serviceDetails['deliveryTimeFrame'];
        }

        $preferredLocation = false;
        if (isset($selectedServices['preferredLocation']) && isset($serviceDetails['preferredLocation'])) {
            $preferredLocation = $serviceDetails['preferredLocation'];
        }

        $preferredNeighbour = false;
        if (isset($selectedServices['preferredNeighbour']) && isset($serviceDetails['preferredNeighbour'])) {
            $preferredNeighbour = $serviceDetails['preferredNeighbour'];
        }

        $parcelAnnouncement = false;
        if (isset($selectedServices['parcelAnnouncement'])) {
            $parcelAnnouncement = true;
        }

        $visualCheckOfAge = false;
        if (isset($selectedServices['visualCheckOfAge']) && isset($serviceDetails['visualCheckOfAge'])) {
            $visualCheckOfAge = $serviceDetails['visualCheckOfAge'];
        }

        $returnShipment = false;
        if (isset($selectedServices['returnShipment'])) {
            $returnShipment = true;
        }

        $insurance = false;
        if (isset($selectedServices['insurance']) && isset($serviceDetails['insurance'])) {
            $insurance = $serviceDetails['insurance'];
        }

        $bulkyGoods = false;
        if (isset($selectedServices['bulkyGoods'])) {
            $bulkyGoods = true;
        }

        return new ShipmentOrder\Settings\ServiceSettings(
            $dayOfDelivery,
            $deliveryTimeFrame,
            $preferredLocation,
            $preferredNeighbour,
            $parcelAnnouncement,
            $visualCheckOfAge,
            $returnShipment,
            $insurance,
            $bulkyGoods
        );
    }
}
