<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Test_Model_Logger_FileTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     */
    public function logFile()
    {
        $message = 'foo';
        $zendLevel = Zend_Log::DEBUG;
        $psrLevel = \Psr\Log\LogLevel::DEBUG;
        $filename = 'foo.log';

        $writer = $this->getMockBuilder(Dhl_Versenden_Model_Logger_Writer::class)
            ->setMethods(['log', 'logException'])
            ->getMock();
        $writer
            ->expects(static::once())
            ->method('log')
            ->with($message, $zendLevel, $filename);
        $writer
            ->expects(static::never())
            ->method('logException');

        $fileLogger = new Dhl_Versenden_Model_Logger_Mage($writer, $filename);
        $fileLogger->log($psrLevel, $message);
    }

    /**
     * @test
     */
    public function interpolate()
    {
        $template = '{foo} XX {bar}';
        $context = [
            'foo' => 'fox',
            'bar' => 'baz',
        ];
        // Logger appends remaining context as JSON after interpolation
        $expectedMessage = 'fox XX baz {"foo":"fox","bar":"baz"}';
        $filename = 'dhl_versenden.log';

        $zendLevel = Zend_Log::DEBUG;
        $psrLevel = \Psr\Log\LogLevel::DEBUG;

        $writer = $this->getMockBuilder(Dhl_Versenden_Model_Logger_Writer::class)
            ->setMethods(['log', 'logException'])
            ->getMock();
        $writer
            ->expects(static::once())
            ->method('log')
            ->with($expectedMessage, $zendLevel, $filename, true);
        $writer
            ->expects(static::never())
            ->method('logException');

        $fileLogger = new Dhl_Versenden_Model_Logger_Mage($writer);
        $fileLogger->log($psrLevel, $template, $context);
    }

    /**
     * @test
     */
    public function logException()
    {
        $message  = 'foo';
        $exception = new SoapFault('1000', $message);
        $context = ['exception' => $exception,];
        $filename = 'dhl_versenden.log';

        $zendLevel = Zend_Log::DEBUG;
        $psrLevel = \Psr\Log\LogLevel::DEBUG;

        $writer = $this->getMockBuilder(Dhl_Versenden_Model_Logger_Writer::class)
            ->setMethods(['log', 'logException'])
            ->getMock();
        $writer
            ->expects(static::once())
            ->method('log')
            ->with($message, $zendLevel, $filename);
        $writer
            ->expects(static::once())
            ->method('logException')
            ->with($exception);

        $fileLogger = new Dhl_Versenden_Model_Logger_Mage($writer);
        $fileLogger->log($psrLevel, $message, $context);
    }
}
