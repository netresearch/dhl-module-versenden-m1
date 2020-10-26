<?php

/**
 * See LICENSE.md for license details.
 */

use Dhl\Versenden\Cig\Configuration as CigConfiguration;

class Dhl_Versenden_Model_Webservice_Gateway_Rest
{
    /**
     * @var Dhl_Versenden_Model_Config_Shipper
     */
    protected $config;

    public function __construct()
    {
        $this->config = Mage::getModel('dhl_versenden/config_shipper');
    }

    /**
     * @param string $date
     * @param string $zip
     * @return \Dhl\Versenden\Cig\Model\AvailableServicesMap
     * @throws Exception
     */
    public function checkoutRecipientZipAvailableServicesGet($date, $zip)
    {
        $account = $this->config->getAccountSettings();
        $ekp = $account->getEkp();
        $user = $this->config->getWebserviceAuthUsername();
        $signature = $this->config->getWebserviceAuthPassword();
        $cgiConfig = new CigConfiguration();
        $cgiConfig->setApiKey(
            'DPDHL-User-Authentication-Token', base64_encode($this->config->getParcelmanagementApiKey())
        );
        $cgiConfig->setUsername($user);
        $cgiConfig->setPassword($signature);
        $cgiConfig->setHost($this->config->getParcelManagementEndpoint());
        $client = new \Dhl\Versenden\Cig\Api\CheckoutApi($cgiConfig);
        $date = new \DateTime($date);
        $response = $client->checkoutRecipientZipAvailableServicesGet($ekp, $zip, $date);

        return $response;
    }

}
