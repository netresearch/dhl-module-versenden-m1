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
            ->setMethods(array('log', 'logException'))
            ->getMock();
        $writer
            ->expects($this->once())
            ->method('log')
            ->with($message, $zendLevel, $filename);
        $writer
            ->expects($this->never())
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
        $message  = 'fox XX baz';
        $context = array(
            'foo' => 'fox',
            'bar' => 'baz',
        );
        $filename = 'dhl_versenden.log';

        $zendLevel = Zend_Log::DEBUG;
        $psrLevel = \Psr\Log\LogLevel::DEBUG;

        $writer = $this->getMockBuilder(Dhl_Versenden_Model_Logger_Writer::class)
            ->setMethods(array('log', 'logException'))
            ->getMock();
        $writer
            ->expects($this->once())
            ->method('log')
            ->with($message, $zendLevel, $filename);
        $writer
            ->expects($this->never())
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
        $context = array('exception' => $exception,);
        $filename = 'dhl_versenden.log';

        $zendLevel = Zend_Log::DEBUG;
        $psrLevel = \Psr\Log\LogLevel::DEBUG;

        $writer = $this->getMockBuilder(Dhl_Versenden_Model_Logger_Writer::class)
            ->setMethods(array('log', 'logException'))
            ->getMock();
        $writer
            ->expects($this->once())
            ->method('log')
            ->with($message, $zendLevel, $filename);
        $writer
            ->expects($this->once())
            ->method('logException')
            ->with($exception);

        $fileLogger = new Dhl_Versenden_Model_Logger_Mage($writer);
        $fileLogger->log($psrLevel, $message, $context);
    }
}
