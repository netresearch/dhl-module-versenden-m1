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
use \Dhl\Versenden\Webservice\Parser\Soap as SoapParser;
/**
 * Dhl_Versenden_Test_Model_Webservice_AdapterTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Test_Model_Webservice_SoapAdapterTest
    extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     */
    public function getVersion()
    {
        $this->markTestIncomplete('test must not perform actual webservice call');

        $major = '2';
        $minor = '1';

        //FIXME(nr): test must not perform actual webservice call, mock response!
        $adapter = Mage::helper('dhl_versenden/webservice')
            ->getWebserviceAdapter(Dhl_Versenden_Model_Webservice_Gateway::SOAP);

        $requestData = new Webservice\RequestData\Version($major, $minor, null);
        $parser = new SoapParser\Version();

        /** @var Webservice\ResponseData\Version $response */
        $response = $adapter->getVersion($requestData, $parser);
        $this->assertInstanceOf(Webservice\ResponseData\Version::class, $response);
        $this->assertStringStartsWith($major, $response->getVersion());
    }

    /**
     * @test
     * @markTestIncomplete
     */
    public function createShipmentOrder()
    {
        $this->markTestIncomplete('test must not perform actual webservice call');

        $adapter = Mage::helper('dhl_versenden/webservice')
            ->getWebserviceAdapter(Dhl_Versenden_Model_Webservice_Gateway::SOAP);

        $requestData = new Webservice\RequestData\CreateShipment(
            new Webservice\RequestData\Version('2', '1', null),
            new Webservice\RequestData\ShipmentOrderCollection()
        );
        $parser = new SoapParser\CreateShipmentOrder();

        $response = $adapter->createShipmentOrder($requestData, $parser);
        $this->assertInstanceOf(Webservice\ResponseData\CreateShipment::class, $response);
    }
}
