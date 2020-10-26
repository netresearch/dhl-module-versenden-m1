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
        $configMock = $this->getModelMock('dhl_versenden/config', array('isLoggingEnabled'));
        $configMock
            ->expects($this->any())
            ->method('isLoggingEnabled')
            ->withConsecutive(
                array(Zend_Log::DEBUG),
                array(Zend_Log::WARN),
                array(Zend_Log::ERR)
            )
            ->willReturnOnConsecutiveCalls($debugEnabled, $warningEnabled, $errorEnabled);
        $this->replaceByMock('model', 'dhl_versenden/config', $configMock);
    }

    /**
     * @test
     * @expectedException Mage_Core_Exception
     */
    public function configArgMissing()
    {
        Mage::getModel('dhl_versenden/log');
    }

    /**
     * @test
     * @expectedException Mage_Core_Exception
     */
    public function configArgInvalid()
    {
        $config = new Varien_Object();
        Mage::getModel('dhl_versenden/log', array('config' => $config));
    }

    /**
     * @test
     */
    public function logDisabled()
    {
        $this->mockConfig(false, false, false);

        $fileLogger = $this->getMockBuilder(Dhl_Versenden_Model_Logger_Mage::class)
            ->setMethods(array('debug', 'warning', 'error'))
            ->disableOriginalConstructor()
            ->getMock();
        $fileLogger
            ->expects($this->never())
            ->method('debug');
        $fileLogger
            ->expects($this->never())
            ->method('warning');
        $fileLogger
            ->expects($this->never())
            ->method('error');

        $config = Mage::getModel('dhl_versenden/config');
        $log = Mage::getModel('dhl_versenden/log', array('config' => $config));
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
            ->setMethods(array('debug', 'warning', 'error'))
            ->disableOriginalConstructor()
            ->getMock();
        $fileLogger
            ->expects($this->once())
            ->method('debug');
        $fileLogger
            ->expects($this->never())
            ->method('warning');
        $fileLogger
            ->expects($this->never())
            ->method('error');

        $config = Mage::getModel('dhl_versenden/config');
        $log = Mage::getModel('dhl_versenden/log', array('config' => $config));
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
            ->setMethods(array('debug', 'warning', 'error'))
            ->disableOriginalConstructor()
            ->getMock();
        $fileLogger
            ->expects($this->never())
            ->method('debug');
        $fileLogger
            ->expects($this->once())
            ->method('warning');
        $fileLogger
            ->expects($this->never())
            ->method('error');

        $config = Mage::getModel('dhl_versenden/config');
        $log = Mage::getModel('dhl_versenden/log', array('config' => $config));
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
            ->setMethods(array('debug', 'warning', 'error'))
            ->disableOriginalConstructor()
            ->getMock();
        $fileLogger
            ->expects($this->never())
            ->method('debug');
        $fileLogger
            ->expects($this->never())
            ->method('warning');
        $fileLogger
            ->expects($this->once())
            ->method('error');

        $config = Mage::getModel('dhl_versenden/config');
        $log = Mage::getModel('dhl_versenden/log', array('config' => $config));
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
            ->setMethods(array('debug', 'warning', 'error'))
            ->disableOriginalConstructor()
            ->getMock();
        $fileLogger
            ->expects($this->once())
            ->method('debug');
        $fileLogger
            ->expects($this->once())
            ->method('warning');
        $fileLogger
            ->expects($this->never())
            ->method('error');

        $config = Mage::getModel('dhl_versenden/config');
        $log = Mage::getModel('dhl_versenden/log', array('config' => $config));
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
            ->setMethods(array('debug', 'warning', 'error'))
            ->disableOriginalConstructor()
            ->getMock();
        $fileLogger
            ->expects($this->never())
            ->method('debug');
        $fileLogger
            ->expects($this->once())
            ->method('warning');
        $fileLogger
            ->expects($this->once())
            ->method('error');

        $config = Mage::getModel('dhl_versenden/config');
        $log = Mage::getModel('dhl_versenden/log', array('config' => $config));
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
            ->setMethods(array('debug', 'warning', 'error'))
            ->disableOriginalConstructor()
            ->getMock();
        $fileLogger
            ->expects($this->once())
            ->method('debug');
        $fileLogger
            ->expects($this->never())
            ->method('warning');
        $fileLogger
            ->expects($this->once())
            ->method('error');

        $config = Mage::getModel('dhl_versenden/config');
        $log = Mage::getModel('dhl_versenden/log', array('config' => $config));
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
            ->setMethods(array('debug', 'warning', 'error'))
            ->disableOriginalConstructor()
            ->getMock();
        $fileLogger
            ->expects($this->once())
            ->method('debug');
        $fileLogger
            ->expects($this->once())
            ->method('warning');
        $fileLogger
            ->expects($this->once())
            ->method('error');

        $config = Mage::getModel('dhl_versenden/config');
        $log = Mage::getModel('dhl_versenden/log', array('config' => $config));
        $log->setLogger($fileLogger);
        $log->debug('foo');
        $log->warning('foo');
        $log->error('foo');
    }
}
