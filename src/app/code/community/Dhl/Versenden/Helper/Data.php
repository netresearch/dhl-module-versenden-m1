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
use \Dhl\Versenden\ShippingInfo;
/**
 * Dhl_Versenden_Helper_Data
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Get the currently installed Dhl_Versenden version.
     *
     * @return string
     */
    public function getModuleVersion()
    {
        $moduleName = $this->_getModuleName();
        return (string)Mage::getConfig()->getModuleConfig($moduleName)->version;
    }

    /**
     * split street into street name, number and care of
     *
     * @param string $street
     *
     * @return array
     */
    public function splitStreet($street)
    {
        /*
         * first pattern  | street_name             | required | ([^0-9]+)         | all characters != 0-9
         * second pattern | additional street value | optional | ([0-9]+[ ])*      | numbers + white spaces
         * ignore         |                         |          | [ \t]*            | white spaces and tabs
         * second pattern | street_number           | optional | ([0-9]+[-\w^.]+)? | numbers + any word character
         * ignore         |                         |          | [, \t]*           | comma, white spaces and tabs
         * third pattern  | supplement              | optional | ([^0-9]+.*)?      | all characters != 0-9 + any character except newline
         */
        if (preg_match("/^([^0-9]+)([0-9]+[ ])*[ \t]*([0-9]*[-\w^.]*)?[, \t]*([^0-9]+.*)?\$/", $street, $matches)) {

            //check if street has additional value and add it to streetname
            if (preg_match("/^([0-9]+)?\$/", trim($matches[2]))) {
                $matches[1] = $matches[1] . $matches[2];

            }
            return array(
                'street_name'   => trim($matches[1]),
                'street_number' => isset($matches[3]) ? $matches[3] : '',
                'supplement'    => isset($matches[4]) ? trim($matches[4]) : ''
            );
        }
        return array(
            'street_name'   => $street,
            'street_number' => '',
            'supplement'    => ''
        );
    }

    /**
     * Prepare service settings from OPC form data.
     *
     * @param string[] $services
     * @param string[] $settings
     * @return ShippingInfo\ServiceSettings
     */
    public function getServiceSettings(array $services, array $settings)
    {
        $serviceSettings = new ShippingInfo\ServiceSettings();
        foreach ($services as $service) {
            $serviceSettings->{$service} = $settings[$service];
        }
        return $serviceSettings;
    }

    /**
     * Prepare receiver from OPC shipping address.
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return ShippingInfo\Receiver
     */
    public function getReceiver(Mage_Sales_Model_Quote_Address $address)
    {
        $receiver = new ShippingInfo\Receiver();
        $receiver->name1 = $address->getName();
        $receiver->name2 = $address->getCompany();
        //TODO(nr): splitStreet
        $receiver->streetName = $address->getStreetFull();
        $receiver->zip = $address->getPostcode();
        $receiver->city = $address->getCity();

        $country = Mage::getModel('directory/country')->loadByCode($address->getCountry());
        $receiver->country = $country->getName();
        $receiver->countryISOCode = $country->getIso2Code();
        $receiver->state = $address->getRegion();

        $receiver->phone = $address->getTelephone();
        $receiver->email = $address->getEmail();
        //TODO(nr): packstation
        return $receiver;
    }
}
