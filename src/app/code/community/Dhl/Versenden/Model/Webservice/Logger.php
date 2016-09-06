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
use Psr\Log;
use \Dhl\Versenden\Webservice\Adapter\Soap as SoapAdapter;

/**
 * Dhl_Versenden_Model_Webservice_Gateway
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Benjamin Heuer <benjamin.heuer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Model_Webservice_Logger extends Log\AbstractLogger
{
    /** @var Dhl_Versenden_Model_Config */
    protected $configModel;

    /**
     * Dhl_Versenden_Model_Webservice_Logger constructor.
     */
    public function __construct()
    {
        $this->configModel = Mage::getModel('dhl_versenden/config');
    }

    /**
     * @var array
     */
    protected static $_levelMapping = array(
        Log\LogLevel::EMERGENCY => Zend_Log::EMERG,
        Log\LogLevel::ALERT     => Zend_Log::ALERT,
        Log\LogLevel::CRITICAL  => Zend_Log::CRIT,
        Log\LogLevel::ERROR     => Zend_Log::ERR,
        Log\LogLevel::WARNING   => Zend_Log::WARN,
        Log\LogLevel::NOTICE    => Zend_Log::NOTICE,
        Log\LogLevel::INFO      => Zend_Log::INFO,
        Log\LogLevel::DEBUG     => Zend_Log::DEBUG,
    );

    /**
     * @var string
     */
    protected $_file = 'dhl_versenden.log';

    /**
     * @param string $file
     *
     * @return $this
     */
    public function setFile($file)
    {
        $this->_file = $file;

        return $this;
    }

    /**
     * @param SoapAdapter $adapter
     */
    public function logDebug(SoapAdapter $adapter)
    {
        if ($this->configModel->isLoggingEnabled(Zend_Log::DEBUG)) {
            $this->debug($adapter->getClient()->__getLastRequest());
            $this->debug($adapter->getClient()->__getLastResponseHeaders());
            $this->debug($adapter->getClient()->__getLastResponse());
        }
    }

    /**
     * @param SoapAdapter $adapter
     */
    public function logError(SoapAdapter $adapter)
    {
        if ($this->configModel->isLoggingEnabled(Zend_Log::ERR)) {
            $this->error($adapter->getClient()->__getLastRequest());
            $this->error($adapter->getClient()->__getLastResponseHeaders());
            $this->error($adapter->getClient()->__getLastResponse());
        }
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        Mage::log($message, self::$_levelMapping[$level], $this->_file);
    }
}
