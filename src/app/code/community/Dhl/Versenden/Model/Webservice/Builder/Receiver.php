<?php

/**
 * See LICENSE.md for license details.
 */

use \Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder\Receiver;

class Dhl_Versenden_Model_Webservice_Builder_Receiver
{
    /** @var Mage_Directory_Model_Country */
    protected $_countryDirectory;

    /** @var Dhl_Versenden_Helper_Address */
    protected $_helper;

    /**
     * Dhl_Versenden_Model_Webservice_Builder_Receiver constructor.
     *
     * @param stdClass[] $args
     * @throws Mage_Core_Exception
     */
    public function __construct($args)
    {
        $argName = 'country_directory';
        if (!isset($args[$argName])) {
            Mage::throwException("required argument missing: $argName");
        }

        if (!$args[$argName] instanceof Mage_Directory_Model_Country) {
            Mage::throwException("invalid argument: $argName");
        }

        $this->_countryDirectory = $args[$argName];

        $argName = 'helper';
        if (!isset($args[$argName])) {
            Mage::throwException("required argument missing: $argName");
        }

        if (!$args[$argName] instanceof Dhl_Versenden_Helper_Address) {
            Mage::throwException("invalid argument: $argName");
        }

        $this->_helper = $args[$argName];
    }

    /**
     * Create receiver from given shipping address
     *
     * @param Mage_Sales_Model_Quote_Address|Mage_Sales_Model_Order_Address $address
     * @return Receiver
     */
    public function getReceiver(Mage_Customer_Model_Address_Abstract $address)
    {
        $this->_countryDirectory->loadByCode($address->getCountryId());
        $country        = $this->_countryDirectory->getName();
        $countryISOCode = $this->_countryDirectory->getIso2Code();
        $state          = $address->getRegion();

        $street = $this->_helper->splitStreet($address->getStreetFull());
        $streetName      = $street['street_name'];
        $streetNumber    = $street['street_number'];
        $addressAddition = $street['supplement'];
        $email           = $address->getEmail() ? :  $address->getOrder()->getCustomerEmail();
        $phone           = '';

        /** @var Dhl_Versenden_Model_Config $config */
        $config = Mage::getModel('dhl_versenden/config');

        if ($config->isSendReceiverPhone($address->getOrder()->getStoreId())) {
            $phone = $address->getTelephone();
        }
        
        // let 3rd party extensions add postal facility data
        $facility = new Varien_Object();
        Mage::dispatchEvent(
            'dhl_versenden_fetch_postal_facility', array(
                'customer_address'   => $address,
                'postal_facility' => $facility,
            )
        );

        $packStation = null;
        if ($facility->getData('shop_type') === \Dhl\Versenden\Bcs\Api\Info\Receiver\PostalFacility::TYPE_PACKSTATION) {
            $packStation = new Receiver\Packstation(
                $address->getPostcode(),
                $address->getCity(),
                $country,
                $countryISOCode,
                $state,
                $facility->getData('shop_number'),
                $facility->getData('post_number')
            );
        }

        $postFiliale = null;
        if ($facility->getData('shop_type') === \Dhl\Versenden\Bcs\Api\Info\Receiver\PostalFacility::TYPE_POSTFILIALE) {
            $postFiliale = new Receiver\Postfiliale(
                $address->getPostcode(),
                $address->getCity(),
                $country,
                $countryISOCode,
                $state,
                $facility->getData('shop_number'),
                $facility->getData('post_number')
            );
        }

        $parcelShop = null;
        if ($facility->getData('shop_type') === \Dhl\Versenden\Bcs\Api\Info\Receiver\PostalFacility::TYPE_PAKETSHOP) {
            $parcelShop = new Receiver\ParcelShop(
                $address->getPostcode(),
                $address->getCity(),
                $country,
                $countryISOCode,
                $state,
                $facility->getData('shop_number'),
                $streetName,
                $streetNumber
            );
        }

        return new Receiver(
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
            $phone,
            $email,
            '',
            $packStation,
            $postFiliale,
            $parcelShop
        );
    }
}
