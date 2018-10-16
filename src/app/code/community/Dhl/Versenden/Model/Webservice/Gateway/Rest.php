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
 * @author    Andreas Müller <andreas.mueller@netresearch.de>
 * @copyright 2018 Netresearch GmbH & Co. KG
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.netresearch.de/
 */
use Dhl\Versenden\Cig\Configuration as CigConfiguration;

/**
 * Dhl_Versenden_Model_Webservice_Gateway_Rest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Andreas Müller <andreas.mueller@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     https://www.netresearch.de/
 */
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
