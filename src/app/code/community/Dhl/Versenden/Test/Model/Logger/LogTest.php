<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Test_Model_Logger_LogTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @param bool $debugEnabled
     * @param bool $warningEnabled
     * @param bool $errorEnabled
     */
    protected function mockConfig($debugEnabled, $warningEnabled, $errorEnabled)
    {
        $configMock = $this->getModelMock('dhl_versenden/config', ['isLoggingEnabled']);
        $configMock
            ->expects(static::any())
            ->method('isLoggingEnabled')
            ->withConsecutive(
                [Zend_Log::DEBUG],
                [Zend_Log::WARN],
                [Zend_Log::ERR],
            )
            ->willReturnOnConsecutiveCalls($debugEnabled, $warningEnabled, $errorEnabled);
        $this->replaceByMock('model', 'dhl_versenden/config', $configMock);
    }

    /**
     * @test
     */
    public function configArgMissing()
    {
        $this->expectException(Mage_Core_Exception::class);

        Mage::getModel('dhl_versenden/log');
    }

    /**
     * @test
     */
    public function configArgInvalid()
    {
        $this->expectException(Mage_Core_Exception::class);

        $config = new Varien_Object();
        Mage::getModel('dhl_versenden/log', ['config' => $config]);
    }

    /**
     * @test
     */
    public function logDisabled()
    {
        $this->mockConfig(false, false, false);

        $fileLogger = $this->getMockBuilder(Dhl_Versenden_Model_Logger_Mage::class)
            ->setMethods(['debug', 'warning', 'error'])
            ->disableOriginalConstructor()
            ->getMock();
        $fileLogger
            ->expects(static::never())
            ->method('debug');
        $fileLogger
            ->expects(static::never())
            ->method('warning');
        $fileLogger
            ->expects(static::never())
            ->method('error');

        $config = Mage::getModel('dhl_versenden/config');
        $log = Mage::getModel('dhl_versenden/log', ['config' => $config]);
        $log->setLogger($fileLogger);
        $log->debug('foo');
        $log->warning('foo');
        $log->error('foo');
    }

    /**
     * @test
     */
    public function logDebug()
    {
        $this->mockConfig(true, false, false);

        $fileLogger = $this->getMockBuilder(Dhl_Versenden_Model_Logger_Mage::class)
            ->setMethods(['debug', 'warning', 'error'])
            ->disableOriginalConstructor()
            ->getMock();
        $fileLogger
            ->expects(static::once())
            ->method('debug');
        $fileLogger
            ->expects(static::never())
            ->method('warning');
        $fileLogger
            ->expects(static::never())
            ->method('error');

        $config = Mage::getModel('dhl_versenden/config');
        $log = Mage::getModel('dhl_versenden/log', ['config' => $config]);
        $log->setLogger($fileLogger);
        $log->debug('foo');
        $log->warning('foo');
        $log->error('foo');
    }

    /**
     * @test
     */
    public function logWarning()
    {
        $this->mockConfig(false, true, false);

        $fileLogger = $this->getMockBuilder(Dhl_Versenden_Model_Logger_Mage::class)
            ->setMethods(['debug', 'warning', 'error'])
            ->disableOriginalConstructor()
            ->getMock();
        $fileLogger
            ->expects(static::never())
            ->method('debug');
        $fileLogger
            ->expects(static::once())
            ->method('warning');
        $fileLogger
            ->expects(static::never())
            ->method('error');

        $config = Mage::getModel('dhl_versenden/config');
        $log = Mage::getModel('dhl_versenden/log', ['config' => $config]);
        $log->setLogger($fileLogger);
        $log->debug('foo');
        $log->warning('foo');
        $log->error('foo');
    }

    /**
     * @test
     */
    public function logError()
    {
        $this->mockConfig(false, false, true);

        $fileLogger = $this->getMockBuilder(Dhl_Versenden_Model_Logger_Mage::class)
            ->setMethods(['debug', 'warning', 'error'])
            ->disableOriginalConstructor()
            ->getMock();
        $fileLogger
            ->expects(static::never())
            ->method('debug');
        $fileLogger
            ->expects(static::never())
            ->method('warning');
        $fileLogger
            ->expects(static::once())
            ->method('error');

        $config = Mage::getModel('dhl_versenden/config');
        $log = Mage::getModel('dhl_versenden/log', ['config' => $config]);
        $log->setLogger($fileLogger);
        $log->debug('foo');
        $log->warning('foo');
        $log->error('foo');
    }

    /**
     * @test
     */
    public function logDebugWarning()
    {
        $this->mockConfig(true, true, false);

        $fileLogger = $this->getMockBuilder(Dhl_Versenden_Model_Logger_Mage::class)
            ->setMethods(['debug', 'warning', 'error'])
            ->disableOriginalConstructor()
            ->getMock();
        $fileLogger
            ->expects(static::once())
            ->method('debug');
        $fileLogger
            ->expects(static::once())
            ->method('warning');
        $fileLogger
            ->expects(static::never())
            ->method('error');

        $config = Mage::getModel('dhl_versenden/config');
        $log = Mage::getModel('dhl_versenden/log', ['config' => $config]);
        $log->setLogger($fileLogger);
        $log->debug('foo');
        $log->warning('foo');
        $log->error('foo');
    }

    /**
     * @test
     */
    public function logWarningError()
    {
        $this->mockConfig(false, true, true);

        $fileLogger = $this->getMockBuilder(Dhl_Versenden_Model_Logger_Mage::class)
            ->setMethods(['debug', 'warning', 'error'])
            ->disableOriginalConstructor()
            ->getMock();
        $fileLogger
            ->expects(static::never())
            ->method('debug');
        $fileLogger
            ->expects(static::once())
            ->method('warning');
        $fileLogger
            ->expects(static::once())
            ->method('error');

        $config = Mage::getModel('dhl_versenden/config');
        $log = Mage::getModel('dhl_versenden/log', ['config' => $config]);
        $log->setLogger($fileLogger);
        $log->debug('foo');
        $log->warning('foo');
        $log->error('foo');
    }

    /**
     * @test
     */
    public function logDebugError()
    {
        $this->mockConfig(true, false, true);

        $fileLogger = $this->getMockBuilder(Dhl_Versenden_Model_Logger_Mage::class)
            ->setMethods(['debug', 'warning', 'error'])
            ->disableOriginalConstructor()
            ->getMock();
        $fileLogger
            ->expects(static::once())
            ->method('debug');
        $fileLogger
            ->expects(static::never())
            ->method('warning');
        $fileLogger
            ->expects(static::once())
            ->method('error');

        $config = Mage::getModel('dhl_versenden/config');
        $log = Mage::getModel('dhl_versenden/log', ['config' => $config]);
        $log->setLogger($fileLogger);
        $log->debug('foo');
        $log->warning('foo');
        $log->error('foo');
    }

    /**
     * @test
     */
    public function logEnabled()
    {
        $this->mockConfig(true, true, true);

        $fileLogger = $this->getMockBuilder(Dhl_Versenden_Model_Logger_Mage::class)
            ->setMethods(['debug', 'warning', 'error'])
            ->disableOriginalConstructor()
            ->getMock();
        $fileLogger
            ->expects(static::once())
            ->method('debug');
        $fileLogger
            ->expects(static::once())
            ->method('warning');
        $fileLogger
            ->expects(static::once())
            ->method('error');

        $config = Mage::getModel('dhl_versenden/config');
        $log = Mage::getModel('dhl_versenden/log', ['config' => $config]);
        $log->setLogger($fileLogger);
        $log->debug('foo');
        $log->warning('foo');
        $log->error('foo');
    }
}
