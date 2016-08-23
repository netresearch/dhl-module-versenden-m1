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
use \Dhl\Versenden\Webservice\RequestData\ShipmentOrder\Receiver;
/**
 * Dhl_Versenden_Model_Webservice_Builder_Receiver
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Model_Webservice_Builder_Receiver
{
    /** @var Mage_Directory_Model_Country */
    protected $countryDirectory;

    /** @var Dhl_Versenden_Helper_Data */
    protected $helper;

    /**
     * Dhl_Versenden_Model_Webservice_Builder_Receiver constructor.
     *
     * @param stdClass[] $args
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
        $this->countryDirectory = $args[$argName];

        $argName = 'helper';
        if (!isset($args[$argName])) {
            Mage::throwException("required argument missing: $argName");
        }
        if (!$args[$argName] instanceof Dhl_Versenden_Helper_Data) {
            Mage::throwException("invalid argument: $argName");
        }
        $this->helper = $args[$argName];
    }

    /**
     * Create receiver from given shipping address
     *
     * @param Mage_Sales_Model_Quote_Address|Mage_Sales_Model_Order_Address $address
     * @return Receiver
     */
    public function getReceiver(Mage_Customer_Model_Address_Abstract $address)
    {
        $this->countryDirectory->loadByCode($address->getCountryId());
        $country        = $this->countryDirectory->getName();
        $countryISOCode = $this->countryDirectory->getIso2Code();

        $street = $this->helper->splitStreet($address->getStreetFull());
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
        if ($facility->getData('shop_type') === Receiver\PostalFacility::TYPE_PACKSTATION) {
            $packStation = new Receiver\Packstation(
                $address->getPostcode(),
                $address->getCity(),
                $facility->getData('shop_number'),
                $facility->getData('post_number')
            );
        }

        $postFiliale = null;
        if ($facility->getData('shop_type') === Receiver\PostalFacility::TYPE_POSTFILIALE) {
            $postFiliale = new Receiver\Postfiliale(
                $address->getPostcode(),
                $address->getCity(),
                $facility->getData('shop_number'),
                $facility->getData('post_number')
            );
        }

        $parcelShop = null;
        if ($facility->getData('shop_type') === Receiver\PostalFacility::TYPE_PAKETSHOP) {
            $parcelShop = new Receiver\ParcelShop(
                $address->getPostcode(),
                $address->getCity(),
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
            $address->getTelephone(),
            $address->getEmail(),
            '',
            $packStation,
            $postFiliale,
            $parcelShop
        );
    }
}
