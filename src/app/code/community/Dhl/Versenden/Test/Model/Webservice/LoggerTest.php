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
 * @author    Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
use \Dhl\Versenden\Webservice\Adapter\Soap as SoapAdapter;

/**
 * Dhl_Versenden_Test_Model_Webservice_LoggerTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Test_Model_Webservice_LoggerTest
    extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     * @loadFixture Model_ConfigTest
     */
    public function log()
    {
        $lastRequest         = 'last request';
        $lastResponse        = 'last response';
        $lastResponseHeaders = 'last response headers';

        $clientMock = $this->getMockBuilder(SoapClient::class)
                           ->disableOriginalConstructor()
                           ->setMethods(array('__getLastRequest', '__getLastResponse', '__getLastResponseHeaders'))
                           ->getMock();
        $clientMock
            ->expects($this->exactly(2))
            ->method('__getLastRequest')
            ->willReturn($lastRequest);
        $clientMock
            ->expects($this->exactly(2))
            ->method('__getLastResponse')
            ->willReturn($lastResponse);
        $clientMock
            ->expects($this->exactly(2))
            ->method('__getLastResponseHeaders')
            ->willReturn($lastResponseHeaders);

        $adapter = new SoapAdapter($clientMock);

        $logger = new Dhl_Versenden_Model_Webservice_Logger();
        $logger->logDebug($adapter);
        $logger->logError($adapter);
    }

    /**
     * @test
     */
    public function setFile()
    {
        $logger = new Dhl_Versenden_Model_Webservice_Logger();
        $this->assertInstanceOf('Dhl_Versenden_Model_Webservice_Logger', $logger->setFile('foo_log.log'));
    }
}
