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
use \Netresearch\Dhl\Versenden\Webservice\Adapter\Soap as SoapAdapter;

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
     */
    public function log()
    {
        $lastRequest = 'last request';
        $lastResponse = 'last response';
        $lastResponseHeaders = 'last response headers';

        $clientMock = $this->getMockBuilder(SoapClient::class)
            ->disableOriginalConstructor()
            ->setMethods(array('__getLastRequest', '__getLastResponse', '__getLastResponseHeaders'))
            ->getMock();
        $clientMock
            ->expects($this->exactly(3))
            ->method('__getLastRequest')
            ->willReturn($lastRequest);
        $clientMock
            ->expects($this->exactly(3))
            ->method('__getLastResponse')
            ->willReturn($lastResponse);
        $clientMock
            ->expects($this->exactly(3))
            ->method('__getLastResponseHeaders')
            ->willReturn($lastResponseHeaders);

        $logMock = $this->getMockBuilder(Dhl_Versenden_Model_Log::class)
            ->setMethods(array('error', 'debug', 'warning'))
            ->disableOriginalConstructor()
            ->getMock();
        $logMock
            ->expects($this->exactly(3))
            ->method('error')
            ->withConsecutive(
                array($lastRequest, array()),
                array($lastResponseHeaders, array()),
                array($lastResponse, array())
            );
        $logMock
            ->expects($this->exactly(3))
            ->method('debug')
            ->withConsecutive(
                array($lastRequest, array()),
                array($lastResponseHeaders, array()),
                array($lastResponse, array())
            );
        $logMock
            ->expects($this->exactly(3))
            ->method('warning')
            ->withConsecutive(
                array($lastRequest, array()),
                array($lastResponseHeaders, array()),
                array($lastResponse, array())
            );

        $soapLogger = new Dhl_Versenden_Model_Webservice_Logger_Soap($logMock);
        $adapter = new SoapAdapter($clientMock);

        // assert messages being passed through to logger
        $soapLogger->debug($adapter);
        $soapLogger->error($adapter);
        $soapLogger->warning($adapter);
    }
}
