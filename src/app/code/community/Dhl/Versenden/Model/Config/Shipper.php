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

use Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder\Shipper as Shipper;

/**
 * Dhl_Versenden_Model_Config_Shipper
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Model_Config_Shipper extends Dhl_Versenden_Model_Config
{
    const CONFIG_XML_FIELD_USER            = 'account_user';
    const CONFIG_XML_FIELD_SIGNATURE       = 'account_signature';
    const CONFIG_XML_FIELD_EKP             = 'account_ekp';
    const CONFIG_XML_FIELD_PARTICIPATION   = 'account_participation';

    const CONFIG_XML_FIELD_SANDBOX_USER            = 'sandbox_account_user';
    const CONFIG_XML_FIELD_SANDBOX_SIGNATURE       = 'sandbox_account_signature';
    const CONFIG_XML_FIELD_SANDBOX_EKP             = 'sandbox_account_ekp';
    const CONFIG_XML_FIELD_SANDBOX_PARTICIPATION   = 'sandbox_account_participation';

    const CONFIG_XML_FIELD_BANKDATA_OWNER      = 'bankdata_owner';
    const CONFIG_XML_FIELD_BANKDATA_BANKNAME   = 'bankdata_bankname';
    const CONFIG_XML_FIELD_BANKDATA_IBAN       = 'bankdata_iban';
    const CONFIG_XML_FIELD_BANKDATA_BIC        = 'bankdata_bic';
    const CONFIG_XML_FIELD_BANKDATA_NOTE1      = 'bankdata_note1';
    const CONFIG_XML_FIELD_BANKDATA_NOTE2      = 'bankdata_note2';
    const CONFIG_XML_FIELD_BANKDATA_ACCOUNTREF = 'bankdata_accountreference';


    const CONFIG_XML_FIELD_CONTACT_NAME1 = 'contact_name1';
    const CONFIG_XML_FIELD_CONTACT_NAME2 = 'contact_name2';
    const CONFIG_XML_FIELD_CONTACT_NAME3 = 'contact_name3';
    const CONFIG_XML_FIELD_CONTACT_STREETNAME = 'contact_streetname';
    const CONFIG_XML_FIELD_CONTACT_STREETNUMBER = 'contact_streetnumber';
    const CONFIG_XML_FIELD_CONTACT_ADDITION = 'contact_addition';
    const CONFIG_XML_FIELD_CONTACT_DISPATCHINFO = 'contact_dispatchinfo';
    const CONFIG_XML_FIELD_CONTACT_ZIP = 'contact_zip';
    const CONFIG_XML_FIELD_CONTACT_CITY = 'contact_city';
    const CONFIG_XML_FIELD_CONTACT_COUNTRYID = 'contact_countryid';
    const CONFIG_XML_FIELD_CONTACT_REGION = 'contact_region';
    const CONFIG_XML_FIELD_CONTACT_PHONE = 'contact_phone';
    const CONFIG_XML_FIELD_CONTACT_EMAIL = 'contact_email';
    const CONFIG_XML_FIELD_CONTACT_PERSON = 'contact_person';


    const CONFIG_XML_FIELD_RETURN_USE_SHIPPER = 'returnshipment_use_shipper';
    const CONFIG_XML_FIELD_RETURN_NAME1 = 'returnshipment_name1';
    const CONFIG_XML_FIELD_RETURN_NAME2 = 'returnshipment_name2';
    const CONFIG_XML_FIELD_RETURN_NAME3 = 'returnshipment_name3';
    const CONFIG_XML_FIELD_RETURN_STREETNAME = 'returnshipment_streetname';
    const CONFIG_XML_FIELD_RETURN_STREETNUMBER = 'returnshipment_streetnumber';
    const CONFIG_XML_FIELD_RETURN_ADDITION = 'returnshipment_addition';
    const CONFIG_XML_FIELD_RETURN_DISPATCHINFO = 'returnshipment_dispatchinfo';
    const CONFIG_XML_FIELD_RETURN_ZIP = 'returnshipment_zip';
    const CONFIG_XML_FIELD_RETURN_CITY = 'returnshipment_city';
    const CONFIG_XML_FIELD_RETURN_COUNTRYID = 'returnshipment_countryid';
    const CONFIG_XML_FIELD_RETURN_REGION = 'returnshipment_region';
    const CONFIG_XML_FIELD_RETURN_PHONE = 'returnshipment_phone';
    const CONFIG_XML_FIELD_RETURN_EMAIL = 'returnshipment_email';
    const CONFIG_XML_FIELD_RETURN_PERSON = 'returnshipment_person';

    /**
     * @param mixed $store
     * @return bool
     */
    public function useShipperForReturns($store = null)
    {
        return $this->getStoreConfigFlag(self::CONFIG_XML_FIELD_RETURN_USE_SHIPPER, $store);
    }

    /**
     * TODO(nr): do not create RequestData objects while reading from config.
     * instead, read plain values and convert prior to actual webservice call.
     */

    /**
     * @return Shipper\Account
     */
    public function getAccountSettings()
    {
        if ($this->isSandboxModeEnabled()) {
            $user      = strtolower($this->getStoreConfig(self::CONFIG_XML_FIELD_SANDBOX_USER));
            $signature = $this->getStoreConfig(self::CONFIG_XML_FIELD_SANDBOX_SIGNATURE);
            $ekp       = $this->getStoreConfig(self::CONFIG_XML_FIELD_SANDBOX_EKP);

            $participations = $this->getStoreConfig(self::CONFIG_XML_FIELD_SANDBOX_PARTICIPATION);
        } else {
            $user      = strtolower($this->getStoreConfig(self::CONFIG_XML_FIELD_USER));
            $signature = Mage::helper('core')->decrypt($this->getStoreConfig(self::CONFIG_XML_FIELD_SIGNATURE));
            $ekp       = $this->getStoreConfig(self::CONFIG_XML_FIELD_EKP);

            $participations = $this->getStoreConfig(self::CONFIG_XML_FIELD_PARTICIPATION);
        }

        $participation = array();
        foreach ($participations as $participationEntry) {
            $participation[$participationEntry['procedure']] = $participationEntry['participation'];
        }

        return new Shipper\Account(
            $user,
            $signature,
            $ekp,
            $participation
        );
    }

    /**
     * @param mixed $store
     * @param array $bankRefMap
     * @return Shipper\BankData
     */
    public function getBankData($store = null, $bankRefMap = array())
    {
        $accountOwner = $this->getStoreConfig(self::CONFIG_XML_FIELD_BANKDATA_OWNER, $store);
        $bankName     = $this->getStoreConfig(self::CONFIG_XML_FIELD_BANKDATA_BANKNAME, $store);
        $iban         = $this->getStoreConfig(self::CONFIG_XML_FIELD_BANKDATA_IBAN, $store);
        $bic          = $this->getStoreConfig(self::CONFIG_XML_FIELD_BANKDATA_BIC, $store);
        $noteOne      = $this->getStoreConfig(self::CONFIG_XML_FIELD_BANKDATA_NOTE1, $store);
        $noteTwo      = $this->getStoreConfig(self::CONFIG_XML_FIELD_BANKDATA_NOTE2, $store);
        $accountRef   = $this->getStoreConfig(self::CONFIG_XML_FIELD_BANKDATA_ACCOUNTREF, $store);

        foreach ($bankRefMap as $key => $replace){
            $noteOne = str_replace($key, $replace, $noteOne);
            $noteTwo = str_replace($key, $replace, $noteTwo);
            $accountRef = str_replace($key, $replace, $accountRef);
        }

        return new Shipper\BankData(
            $accountOwner,
            $bankName,
            $iban,
            $bic,
            $noteOne,
            $noteTwo,
            $accountRef
        );
    }

    /**
     * @param mixed $store
     * @return Shipper\Contact
     */
    public function getContact($store = null)
    {
        $countryId = $this->getStoreConfig(self::CONFIG_XML_FIELD_CONTACT_COUNTRYID, $store);
        $countryDirectory = Mage::getSingleton('directory/country')->loadByCode($countryId);

        $nameOne = $this->getStoreConfig(self::CONFIG_XML_FIELD_CONTACT_NAME1, $store);
        $nameTwo = $this->getStoreConfig(self::CONFIG_XML_FIELD_CONTACT_NAME2, $store);
        $nameThree = $this->getStoreConfig(self::CONFIG_XML_FIELD_CONTACT_NAME3, $store);
        $streetName = $this->getStoreConfig(self::CONFIG_XML_FIELD_CONTACT_STREETNAME, $store);
        $streetNumber = $this->getStoreConfig(self::CONFIG_XML_FIELD_CONTACT_STREETNUMBER, $store);
        $addressAddition = $this->getStoreConfig(self::CONFIG_XML_FIELD_CONTACT_ADDITION, $store);
        $dispatchingInformation = $this->getStoreConfig(self::CONFIG_XML_FIELD_CONTACT_DISPATCHINFO, $store);
        $zip = $this->getStoreConfig(self::CONFIG_XML_FIELD_CONTACT_ZIP, $store);
        $city = $this->getStoreConfig(self::CONFIG_XML_FIELD_CONTACT_CITY, $store);
        $country = $countryDirectory->getName();
        $countryISOCode = $countryDirectory->getIso2Code();
        $state = $this->getStoreConfig(self::CONFIG_XML_FIELD_CONTACT_REGION, $store);

        $phone = $this->getStoreConfig(self::CONFIG_XML_FIELD_CONTACT_PHONE, $store);
        $email = $this->getStoreConfig(self::CONFIG_XML_FIELD_CONTACT_EMAIL, $store);
        $contactPerson = $this->getStoreConfig(self::CONFIG_XML_FIELD_CONTACT_PERSON, $store);

        return new Shipper\Contact(
            $nameOne,
            $nameTwo,
            $nameThree,
            $streetName,
            $streetNumber,
            $addressAddition,
            $dispatchingInformation,
            $zip,
            $city,
            $country,
            $countryISOCode,
            $state,
            $phone,
            $email,
            $contactPerson
        );
    }

    /**
     * @param mixed $store
     * @return Shipper\Contact|Shipper\ReturnReceiver
     */
    public function getReturnReceiver($store = null)
    {
        if ($this->useShipperForReturns($store)) {
            return $this->getContact();
        }

        $countryId = $this->getStoreConfig(self::CONFIG_XML_FIELD_RETURN_COUNTRYID, $store);
        $countryDirectory = Mage::getSingleton('directory/country')->loadByCode($countryId);

        $nameOne = $this->getStoreConfig(self::CONFIG_XML_FIELD_RETURN_NAME1, $store);
        $nameTwo = $this->getStoreConfig(self::CONFIG_XML_FIELD_RETURN_NAME2, $store);
        $nameThree = $this->getStoreConfig(self::CONFIG_XML_FIELD_RETURN_NAME3, $store);
        $streetName = $this->getStoreConfig(self::CONFIG_XML_FIELD_RETURN_STREETNAME, $store);
        $streetNumber = $this->getStoreConfig(self::CONFIG_XML_FIELD_RETURN_STREETNUMBER, $store);
        $addressAddition = $this->getStoreConfig(self::CONFIG_XML_FIELD_RETURN_ADDITION, $store);
        $dispatchingInformation = $this->getStoreConfig(self::CONFIG_XML_FIELD_RETURN_DISPATCHINFO, $store);
        $zip = $this->getStoreConfig(self::CONFIG_XML_FIELD_RETURN_ZIP, $store);
        $city = $this->getStoreConfig(self::CONFIG_XML_FIELD_RETURN_CITY, $store);
        $country = $countryDirectory->getName();
        $countryISOCode = $countryDirectory->getIso2Code();
        $state = $this->getStoreConfig(self::CONFIG_XML_FIELD_RETURN_REGION, $store);

        $phone = $this->getStoreConfig(self::CONFIG_XML_FIELD_RETURN_PHONE, $store);
        $email = $this->getStoreConfig(self::CONFIG_XML_FIELD_RETURN_EMAIL, $store);
        $contactPerson = $this->getStoreConfig(self::CONFIG_XML_FIELD_RETURN_PERSON, $store);

        return new Shipper\ReturnReceiver(
            $nameOne,
            $nameTwo,
            $nameThree,
            $streetName,
            $streetNumber,
            $addressAddition,
            $dispatchingInformation,
            $zip,
            $city,
            $country,
            $countryISOCode,
            $state,
            $phone,
            $email,
            $contactPerson
        );
    }

    /**
     * @param mixed $store
     * @return Shipper
     */
    public function getShipper($store = null)
    {
        return new Shipper(
            $this->getAccountSettings(),
            $this->getBankData($store),
            $this->getContact($store),
            $this->getReturnReceiver($store)
        );
    }

    /**
     * Obtain shipper country from module configuration.
     *
     * @see Dhl_Versenden_Model_Config::getShipperCountry()
     * @param mixed $store
     * @return string
     */
    public function getShipperCountry($store = null)
    {
        return $this->getContact($store)->getCountryISOCode();
    }

    /**
     * @return string
     */
    public function getAccountEkp()
    {
        return $this->getStoreConfig(self::CONFIG_XML_FIELD_EKP);
    }

    /**
     * @return string
     */
    public function getParcelmanagementApiKey()
    {
        $accountSettings = $this->getAccountSettings();

        return $accountSettings->getUser() . ':' . $accountSettings->getSignature();
    }
}
