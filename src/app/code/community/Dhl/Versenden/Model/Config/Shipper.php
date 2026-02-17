<?php

/**
 * See LICENSE.md for license details.
 */

use Dhl\Versenden\ParcelDe\Config\Data\Shipper as Shipper;

class Dhl_Versenden_Model_Config_Shipper extends Dhl_Versenden_Model_Config
{
    public const CONFIG_XML_FIELD_USER            = 'account_user';
    public const CONFIG_XML_FIELD_SIGNATURE       = 'account_signature';
    public const CONFIG_XML_FIELD_EKP             = 'account_ekp';
    public const CONFIG_XML_FIELD_PARTICIPATION   = 'account_participation';

    public const CONFIG_XML_FIELD_SANDBOX_USER            = 'sandbox_account_user';
    public const CONFIG_XML_FIELD_SANDBOX_SIGNATURE       = 'sandbox_account_signature';
    public const CONFIG_XML_FIELD_SANDBOX_EKP             = 'sandbox_account_ekp';
    public const CONFIG_XML_FIELD_SANDBOX_PARTICIPATION   = 'sandbox_account_participation';

    public const CONFIG_XML_FIELD_BANKDATA_OWNER      = 'bankdata_owner';
    public const CONFIG_XML_FIELD_BANKDATA_BANKNAME   = 'bankdata_bankname';
    public const CONFIG_XML_FIELD_BANKDATA_IBAN       = 'bankdata_iban';
    public const CONFIG_XML_FIELD_BANKDATA_BIC        = 'bankdata_bic';
    public const CONFIG_XML_FIELD_BANKDATA_NOTE1      = 'bankdata_note1';
    public const CONFIG_XML_FIELD_BANKDATA_NOTE2      = 'bankdata_note2';
    public const CONFIG_XML_FIELD_BANKDATA_ACCOUNTREF = 'bankdata_accountreference';


    public const CONFIG_XML_FIELD_CONTACT_NAME1 = 'contact_name1';
    public const CONFIG_XML_FIELD_CONTACT_NAME2 = 'contact_name2';
    public const CONFIG_XML_FIELD_CONTACT_NAME3 = 'contact_name3';
    public const CONFIG_XML_FIELD_CONTACT_STREETNAME = 'contact_streetname';
    public const CONFIG_XML_FIELD_CONTACT_STREETNUMBER = 'contact_streetnumber';
    public const CONFIG_XML_FIELD_CONTACT_ADDITION = 'contact_addition';
    public const CONFIG_XML_FIELD_CONTACT_DISPATCHINFO = 'contact_dispatchinfo';
    public const CONFIG_XML_FIELD_CONTACT_ZIP = 'contact_zip';
    public const CONFIG_XML_FIELD_CONTACT_CITY = 'contact_city';
    public const CONFIG_XML_FIELD_CONTACT_COUNTRYID = 'contact_countryid';
    public const CONFIG_XML_FIELD_CONTACT_REGION = 'contact_region';
    public const CONFIG_XML_FIELD_CONTACT_PHONE = 'contact_phone';
    public const CONFIG_XML_FIELD_CONTACT_EMAIL = 'contact_email';
    public const CONFIG_XML_FIELD_CONTACT_PERSON = 'contact_person';


    public const CONFIG_XML_FIELD_RETURN_USE_SHIPPER = 'returnshipment_use_shipper';
    public const CONFIG_XML_FIELD_RETURN_NAME1 = 'returnshipment_name1';
    public const CONFIG_XML_FIELD_RETURN_NAME2 = 'returnshipment_name2';
    public const CONFIG_XML_FIELD_RETURN_NAME3 = 'returnshipment_name3';
    public const CONFIG_XML_FIELD_RETURN_STREETNAME = 'returnshipment_streetname';
    public const CONFIG_XML_FIELD_RETURN_STREETNUMBER = 'returnshipment_streetnumber';
    public const CONFIG_XML_FIELD_RETURN_ADDITION = 'returnshipment_addition';
    public const CONFIG_XML_FIELD_RETURN_DISPATCHINFO = 'returnshipment_dispatchinfo';
    public const CONFIG_XML_FIELD_RETURN_ZIP = 'returnshipment_zip';
    public const CONFIG_XML_FIELD_RETURN_CITY = 'returnshipment_city';
    public const CONFIG_XML_FIELD_RETURN_COUNTRYID = 'returnshipment_countryid';
    public const CONFIG_XML_FIELD_RETURN_REGION = 'returnshipment_region';
    public const CONFIG_XML_FIELD_RETURN_PHONE = 'returnshipment_phone';
    public const CONFIG_XML_FIELD_RETURN_EMAIL = 'returnshipment_email';
    public const CONFIG_XML_FIELD_RETURN_PERSON = 'returnshipment_person';

    /**
     * @param mixed $store
     * @return bool
     */
    public function useShipperForReturns($store = null)
    {
        return $this->getStoreConfigFlag(self::CONFIG_XML_FIELD_RETURN_USE_SHIPPER, $store);
    }

    // phpcs:disable Generic.Commenting.Todo.TaskFound
    /**
     * TODO(nr): do not create RequestData objects while reading from config.
     * instead, read plain values and convert prior to actual webservice call.
     */
    // phpcs:enable Generic.Commenting.Todo.TaskFound

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

        $participation = [];
        foreach ($participations as $participationEntry) {
            $participation[$participationEntry['procedure']] = $participationEntry['participation'];
        }

        return new Shipper\Account(
            $user,
            $signature,
            $ekp,
            $participation,
        );
    }

    /**
     * @param mixed $store
     * @param array $bankRefMap
     * @return Shipper\BankData
     */
    public function getBankData($store = null, $bankRefMap = [])
    {
        $accountOwner = $this->getStoreConfig(self::CONFIG_XML_FIELD_BANKDATA_OWNER, $store);
        $bankName     = $this->getStoreConfig(self::CONFIG_XML_FIELD_BANKDATA_BANKNAME, $store);
        $iban         = $this->getStoreConfig(self::CONFIG_XML_FIELD_BANKDATA_IBAN, $store);
        $bic          = $this->getStoreConfig(self::CONFIG_XML_FIELD_BANKDATA_BIC, $store);
        $noteOne      = $this->getStoreConfig(self::CONFIG_XML_FIELD_BANKDATA_NOTE1, $store);
        $noteTwo      = $this->getStoreConfig(self::CONFIG_XML_FIELD_BANKDATA_NOTE2, $store);
        $accountRef   = $this->getStoreConfig(self::CONFIG_XML_FIELD_BANKDATA_ACCOUNTREF, $store);

        foreach ($bankRefMap as $key => $replace) {
            $noteOne = str_replace($key, (string) $replace, (string) $noteOne);
            $noteTwo = str_replace($key, (string) $replace, (string) $noteTwo);
            $accountRef = str_replace($key, (string) $replace, (string) $accountRef);
        }

        return new Shipper\BankData(
            $accountOwner,
            $bankName,
            $iban,
            $bic,
            $noteOne,
            $noteTwo,
            $accountRef,
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
            $contactPerson,
        );
    }

    /**
     * @param mixed $store
     * @return Shipper\Contact|Shipper\ReturnReceiver
     */
    public function getReturnReceiver($store = null)
    {
        if ($this->useShipperForReturns($store)) {
            return $this->getContact($store);
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
            $contactPerson,
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
            $this->getReturnReceiver($store),
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

    /**
     * Get DHL REST API App Token
     *
     * @return string
     */
    public function getAppToken()
    {
        return $this->getStoreConfig(self::CONFIG_XML_PATH_APP_TOKEN);
    }
}
