<?php

/**
 * See LICENSE.md for license details.
 */

use \Dhl\Versenden\Bcs\Api\Webservice\Adapter\Soap as SoapAdapter;

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
