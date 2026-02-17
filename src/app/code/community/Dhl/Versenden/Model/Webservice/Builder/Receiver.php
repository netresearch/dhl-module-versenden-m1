<?php

/**
 * See LICENSE.md for license details.
 */


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
     * Build receiver data into SDK request builder
     *
     * @param \Dhl\Sdk\ParcelDe\Shipping\Api\ShipmentOrderRequestBuilderInterface $sdkBuilder
     * @param Mage_Sales_Model_Quote_Address|Mage_Sales_Model_Order_Address $address
     * @param bool $includeRecipientEmail Whether to include recipient email (enables DHL parcel notification)
     * @return void
     */
    public function build(\Dhl\Sdk\ParcelDe\Shipping\Api\ShipmentOrderRequestBuilderInterface $sdkBuilder, Mage_Customer_Model_Address_Abstract $address, bool $includeRecipientEmail = true)
    {
        $this->_countryDirectory->loadByCode($address->getCountryId());
        // Convert country code from ISO-2 to ISO-3 for REST API
        // The legacy BCS/SOAP receiver uses ISO-2, but REST SDK requires ISO-3
        $countryISOCode = $this->_countryDirectory->getIso3Code();
        $state          = $address->getRegion();

        $street = $this->_helper->splitStreet($address->getStreetFull());
        $streetName      = $street['street_name'];
        $streetNumber    = $street['street_number'];
        $addressAddition = $street['supplement'];
        $email = $includeRecipientEmail
            ? ($address->getEmail() ?: $address->getOrder()->getCustomerEmail())
            : null;
        $phone           = '';

        /** @var Dhl_Versenden_Model_Config $config */
        $config = Mage::getModel('dhl_versenden/config');

        if ($config->isSendReceiverPhone($address->getOrder()->getStoreId())) {
            $phone = $address->getTelephone();
        }

        // let 3rd party extensions add postal facility data
        $facility = new Varien_Object();
        Mage::dispatchEvent(
            'dhl_versenden_fetch_postal_facility',
            [
                'customer_address'   => $address,
                'postal_facility' => $facility,
            ],
        );

        // Check for postal facility types
        if ($facility->getData('shop_type') === \Dhl\Versenden\ParcelDe\Info\Receiver\PostalFacility::TYPE_PACKSTATION) {
            $sdkBuilder->setPackstation(
                $address->getName(),
                $facility->getData('post_number'),
                $facility->getData('shop_number'),
                $countryISOCode,
                $address->getPostcode(),
                $address->getCity(),
                $state,
                null,
            );
            return;
        }

        if ($facility->getData('shop_type') === \Dhl\Versenden\ParcelDe\Info\Receiver\PostalFacility::TYPE_POSTFILIALE) {
            $sdkBuilder->setPostfiliale(
                $address->getName(),
                $facility->getData('shop_number'),
                $countryISOCode,
                $address->getPostcode(),
                $address->getCity(),
                null,
                $facility->getData('post_number'),
                $state,
                null,
            );
            return;
        }

        // Default: regular recipient address (includes parcel shop fallback)
        $sdkBuilder->setRecipientAddress(
            $address->getName(),
            $countryISOCode,
            $address->getPostcode(),
            $address->getCity(),
            $streetName,
            $streetNumber,
            $address->getCompany(),
            null,
            $email,
            $phone,
            null,
            $state,
            null,
            !empty($addressAddition) ? [$addressAddition] : [],
        );
    }
}
