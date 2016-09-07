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

/**
 * Dhl_Versenden_Test_Model_Logger_LogTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Test_Model_Logger_LogTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @param bool $debugEnabled
     * @param bool $errorEnabled
     */
    protected function mockConfig($debugEnabled, $errorEnabled)
    {
        $configMock = $this->getModelMock('dhl_versenden/config', array('isLoggingEnabled'));
        $configMock
            ->expects($this->any())
            ->method('isLoggingEnabled')
            ->withConsecutive(
                array(Zend_Log::DEBUG),
                array(Zend_Log::ERR)
            )
            ->willReturnOnConsecutiveCalls($debugEnabled, $errorEnabled);
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
    public function logErrorDisabled()
    {
        $this->mockConfig(true, false);

        $fileLogger = $this->getMockBuilder(Dhl_Versenden_Model_Logger_Mage::class)
            ->setMethods(array('debug', 'error'))
            ->disableOriginalConstructor()
            ->getMock();
        $fileLogger
            ->expects($this->once())
            ->method('debug');
        $fileLogger
            ->expects($this->never())
            ->method('error');

        $config = Mage::getModel('dhl_versenden/config');
        $log = Mage::getModel('dhl_versenden/log', array('config' => $config));
        $log->setLogger($fileLogger);
        $log->debug('foo');
        $log->error('foo');
    }

    /**
     * @test
     */
    public function logDebugDisabled()
    {
        $this->mockConfig(false, true);

        $fileLogger = $this->getMockBuilder(Dhl_Versenden_Model_Logger_Mage::class)
            ->setMethods(array('debug', 'error'))
            ->disableOriginalConstructor()
            ->getMock();
        $fileLogger
            ->expects($this->never())
            ->method('debug');
        $fileLogger
            ->expects($this->once())
            ->method('error');

        $config = Mage::getModel('dhl_versenden/config');
        $log = Mage::getModel('dhl_versenden/log', array('config' => $config));
        $log->setLogger($fileLogger);
        $log->debug('foo');
        $log->error('foo');
    }

    /**
     * @test
     */
    public function logEnabled()
    {
        $this->mockConfig(true, true);

        $fileLogger = $this->getMockBuilder(Dhl_Versenden_Model_Logger_Mage::class)
            ->setMethods(array('debug', 'error'))
            ->disableOriginalConstructor()
            ->getMock();
        $fileLogger
            ->expects($this->once())
            ->method('debug');
        $fileLogger
            ->expects($this->once())
            ->method('error');

        $config = Mage::getModel('dhl_versenden/config');
        $log = Mage::getModel('dhl_versenden/log', array('config' => $config));
        $log->setLogger($fileLogger);
        $log->debug('foo');
        $log->error('foo');
    }

    /**
     * @test
     */
    public function logDisabled()
    {
        $this->mockConfig(false, false);

        $fileLogger = $this->getMockBuilder(Dhl_Versenden_Model_Logger_Mage::class)
            ->setMethods(array('debug', 'error'))
            ->disableOriginalConstructor()
            ->getMock();
        $fileLogger
            ->expects($this->never())
            ->method('debug');
        $fileLogger
            ->expects($this->never())
            ->method('error');

        $config = Mage::getModel('dhl_versenden/config');
        $log = Mage::getModel('dhl_versenden/log', array('config' => $config));
        $log->setLogger($fileLogger);
        $log->debug('foo');
        $log->error('foo');
    }
}
