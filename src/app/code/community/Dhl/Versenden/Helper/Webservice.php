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
class Dhl_Versenden_Helper_Webservice extends Mage_Core_Helper_Abstract
{
    const ADAPTER_TYPE_SOAP = 'soap';

    /**
     * @return Webservice\Adapter\Soap
     */
    protected function getSoapAdapter()
    {
        $config = Mage::getModel('dhl_versenden/config');

        $options = array(
            'location' => $config->getEndpoint(),
            'login' => $config->getWebserviceAuthUsername(),
            'password' => $config->getWebserviceAuthPassword(),
        );
        $client = new \Dhl\Bcs\Api\GVAPI_2_0_de($options);

        $authHeader = new \SoapHeader(
            'http://dhl.de/webservice/cisbase',
            'Authentification',
            array(
                'user' => $config->getAuthenticationUser(),
                'signature' => $config->getAuthenticationSignature(),
            )
        );
        $client->__setSoapHeaders($authHeader);

        $adapter = new Webservice\Adapter\Soap($client);

        return $adapter;
    }

    /**
     * @param string $type
     * @return Webservice\Adapter
     */
    public function getWebserviceAdapter($type)
    {
        $adapter = null;

        switch ($type) {
            case self::ADAPTER_TYPE_SOAP:
            default:
                $adapter = $this->getSoapAdapter();
        }

        return $adapter;
    }
}
