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
use \Dhl\Versenden\Webservice\RequestData\ShipmentOrder;
use \Dhl\Versenden\Webservice\RequestData\ShipmentOrder\Shipper as ShipperData;
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
    const CONFIG_XML_FIELD_GOGREEN_ENABLED = 'account_gogreen_enabled';

    const CONFIG_XML_FIELD_SANDBOX_USER            = 'sandbox_account_user';
    const CONFIG_XML_FIELD_SANDBOX_SIGNATURE       = 'sandbox_account_signature';
    const CONFIG_XML_FIELD_SANDBOX_EKP             = 'sandbox_account_ekp';
    const CONFIG_XML_FIELD_SANDBOX_PARTICIPATION   = 'sandbox_account_participation';
    const CONFIG_XML_FIELD_SANDBOX_GOGREEN_ENABLED = 'sandbox_account_gogreen_enabled';


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
     * @param mixed $store
     * @return ShipperData\Account
     */
    public function getAccountSettings($store = null)
    {
        if (!$this->isSandboxModeEnabled($store)) {
            $user      = $this->getStoreConfig(self::CONFIG_XML_FIELD_USER, $store);
            $signature = $this->getStoreConfig(self::CONFIG_XML_FIELD_SIGNATURE, $store);
            $ekp       = $this->getStoreConfig(self::CONFIG_XML_FIELD_EKP, $store);
            $goGreen   = $this->getStoreConfigFlag(self::CONFIG_XML_FIELD_GOGREEN_ENABLED, $store);

            $participation = $this->getStoreConfig(self::CONFIG_XML_FIELD_PARTICIPATION, $store);
        } else {
            $user      = $this->getStoreConfig(self::CONFIG_XML_FIELD_SANDBOX_USER, $store);
            $signature = $this->getStoreConfig(self::CONFIG_XML_FIELD_SANDBOX_SIGNATURE, $store);
            $ekp       = $this->getStoreConfig(self::CONFIG_XML_FIELD_SANDBOX_EKP, $store);
            $goGreen   = $this->getStoreConfigFlag(self::CONFIG_XML_FIELD_SANDBOX_GOGREEN_ENABLED, $store);

            $participation = $this->getStoreConfig(self::CONFIG_XML_FIELD_SANDBOX_PARTICIPATION, $store);
        }

        return new ShipperData\Account(
            $user,
            $signature,
            $ekp,
            $goGreen,
            $participation
        );
    }

    /**
     * @param mixed $store
     * @return ShipperData\BankData
     */
    public function getBankData($store = null)
    {
        $accountOwner = $this->getStoreConfig(self::CONFIG_XML_FIELD_BANKDATA_OWNER, $store);
        $bankName     = $this->getStoreConfig(self::CONFIG_XML_FIELD_BANKDATA_BANKNAME, $store);
        $iban         = $this->getStoreConfig(self::CONFIG_XML_FIELD_BANKDATA_IBAN, $store);
        $bic          = $this->getStoreConfig(self::CONFIG_XML_FIELD_BANKDATA_BIC, $store);
        $note1        = $this->getStoreConfig(self::CONFIG_XML_FIELD_BANKDATA_NOTE1, $store);
        $note2        = $this->getStoreConfig(self::CONFIG_XML_FIELD_BANKDATA_NOTE2, $store);
        $accountRef   = $this->getStoreConfig(self::CONFIG_XML_FIELD_BANKDATA_ACCOUNTREF, $store);

        return new ShipperData\BankData(
            $accountOwner,
            $bankName,
            $iban,
            $bic,
            $note1,
            $note2,
            $accountRef
        );
    }

    /**
     * @param mixed $store
     * @return ShipperData\Contact
     */
    public function getContact($store = null)
    {
        $countryId = $this->getStoreConfig(self::CONFIG_XML_FIELD_CONTACT_COUNTRYID, $store);
        $countryDirectory = Mage::getSingleton('directory/country')->loadByCode($countryId);

        $name1 = $this->getStoreConfig(self::CONFIG_XML_FIELD_CONTACT_NAME1, $store);
        $name2 = $this->getStoreConfig(self::CONFIG_XML_FIELD_CONTACT_NAME2, $store);
        $name3 = $this->getStoreConfig(self::CONFIG_XML_FIELD_CONTACT_NAME3, $store);
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

        return new ShipperData\Contact(
            $name1,
            $name2,
            $name3,
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
     * @return ShipperData\Contact|ShipperData\ReturnReceiver
     */
    public function getReturnReceiver($store = null)
    {
        if ($this->useShipperForReturns($store)) {
            return $this->getContact();
        }

        $countryId = $this->getStoreConfig(self::CONFIG_XML_FIELD_RETURN_COUNTRYID, $store);
        $countryDirectory = Mage::getSingleton('directory/country')->loadByCode($countryId);

        $name1 = $this->getStoreConfig(self::CONFIG_XML_FIELD_RETURN_NAME1, $store);
        $name2 = $this->getStoreConfig(self::CONFIG_XML_FIELD_RETURN_NAME2, $store);
        $name3 = $this->getStoreConfig(self::CONFIG_XML_FIELD_RETURN_NAME3, $store);
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

        return new ShipperData\ReturnReceiver(
            $name1,
            $name2,
            $name3,
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
     * @return ShipmentOrder\Shipper
     */
    public function getShipper($store = null)
    {
        return new ShipmentOrder\Shipper(
            $this->getAccountSettings($store),
            $this->getBankData($store),
            $this->getContact($store),
            $this->getReturnReceiver($store)
        );
    }
}
