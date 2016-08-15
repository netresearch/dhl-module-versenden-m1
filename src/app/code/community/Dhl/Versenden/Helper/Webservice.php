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
    /**
     * Create receiver from given shipping address
     *
     * @param Mage_Sales_Model_Quote_Address|Mage_Sales_Model_Order_Address $address
     * @return ShipmentOrder\Receiver
     */
    public function shippingAddressToReceiver(Mage_Customer_Model_Address_Abstract $address)
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
        Mage::dispatchEvent(
            'dhl_versenden_set_postal_facility', array(
                'quote_address'   => $address,
                'postal_facility' => $facility,
            )
        );

        $packStation = null;
        if ($facility->getData('shop_type') === ShipmentOrder\Receiver\PostalFacility::TYPE_PACKSTATION) {
            $packStation = new ShipmentOrder\Receiver\Packstation(
                $address->getPostcode(),
                $address->getCity(),
                $facility->getData('shop_number'),
                $facility->getData('post_number')
            );
        }

        $postFiliale = null;
        if ($facility->getData('shop_type') === ShipmentOrder\Receiver\PostalFacility::TYPE_POSTFILIALE) {
            $postFiliale = new ShipmentOrder\Receiver\Postfiliale(
                $address->getPostcode(),
                $address->getCity(),
                $facility->getData('shop_number'),
                $facility->getData('post_number')
            );
        }

        $parcelShop = null;
        if ($facility->getData('shop_type') === ShipmentOrder\Receiver\PostalFacility::TYPE_PAKETSHOP) {
            $parcelShop = new ShipmentOrder\Receiver\ParcelShop(
                $address->getPostcode(),
                $address->getCity(),
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
     * Convert service information to standardized data object. Service info
     * is usually derived from POST data in the following form:
     * - selected_services: checkboxes
     * - service_details: text input or dropdowns
     *
     * @param bool[] $selectedServices
     * @param string[] $serviceDetails
     * @return ShipmentOrder\ServiceSelection
     */
    public function serviceSelectionToServiceSettings(array $selectedServices, array $serviceDetails)
    {
        $settings = array();

        foreach ($selectedServices as $name => $isSelected) {
            if ($isSelected) {
                $settings[$name] = isset($serviceDetails[$name]) ? $serviceDetails[$name] : true;
            }
        }

        return ShipmentOrder\ServiceSelection::fromArray($settings);
    }

    /**
     * Find highest value, package weight or package min weight.
     * Weight is returned with respect to the global unit_of_measure setting (g or kg).
     *
     * @param float $packageWeight
     * @return float
     */
    public function getPackageWeight(ShipmentOrder\GlobalSettings $settings, $packageWeight)
    {
        $unit = $settings->getUnitOfMeasure();
        $minWeight = Dhl_Versenden_Model_Shipping_Carrier_Versenden::PACKAGE_MIN_WEIGHT;
        if ($unit == 'G') {
            $minWeight *= 1000;
        }

        return max($packageWeight, $minWeight);
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @param string $message
     * @param string $messageType
     */
    public function addStatusHistoryComment(Mage_Sales_Model_Order $order, $message, $messageType)
    {
        // TODO(nr): use psr log types
        // TODO(nr): add dhl message type indicator, i.e. some icon
        if ($messageType === Zend_Log::ERR) {
            $message = sprintf('%s %s', '(x)', $message);
        } else {
            $message = sprintf('%s %s', '(i)', $message);
        }

        $history = Mage::getModel('sales/order_status_history')
            ->setOrder($order)
            ->setStatus($order->getStatus())
            ->setComment($message)
            ->setData('entity_name', Mage_Sales_Model_Order::HISTORY_ENTITY_NAME);

        $historyCollection = $order->getStatusHistoryCollection();
        $historyCollection->addItem($history);
        $historyCollection->save();
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @param string $message
     */
    public function addStatusHistoryError(Mage_Sales_Model_Order $order, $message)
    {
        $this->addStatusHistoryComment($order, $message, Zend_Log::ERR);
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @param string $message
     */
    public function addStatusHistoryInfo(Mage_Sales_Model_Order $order, $message)
    {
        $this->addStatusHistoryComment($order, $message, Zend_Log::INFO);
    }
}
