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
 * @author    andreas <andreas.mueller@netresearch.de>
 * @copyright 2018 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.netresearch.de/
 */

class Dhl_Versenden_Model_Tracking
{
    /**
     * @var Dhl_Versenden_Model_Config
     */
    protected $config;
    /**
     * @var Mage_Core_Model_Date
     */
    protected $dateModel;

    protected $timeformat = 'Y-m-d';

    /**
     * Dhl_Versenden_Model_Tracking constructor
     */
    public function __construct()
    {
        $this->config = Mage::getModel('dhl_versenden/config');
        $this->dateModel = Mage::getSingleton('core/date');
    }

    /**
     * @return bool
     */
    public function canExecute()
    {
        if (!$this->isAvailable()) {
            return false;
        }
        $currentDate = $this->dateModel->date($this->timeformat);
        $nextDate = $this->checkInterval($currentDate);
        if ($nextDate) {
            $this->saveNewDate($nextDate);
            return true;
        }

        return false;
    }

    /**
     * @param $date
     * @return bool|string
     * @throws Exception
     */
    protected function checkInterval($date)
    {
        $dateTime = new DateTime($date);
        $interval = $this->config->getTrackingInterval();
        $tmpDate = $dateTime->add(new DateInterval("P{$interval}D"));
        $nextTrackConfig = $this->config->getNextTrackDate();
        $nextPossibleDay = $tmpDate->format($this->timeformat);
        if (!$nextTrackConfig || $date === $nextTrackConfig) {
            return $nextPossibleDay;
        }

        return false;
    }

    /**
     * @param $date
     */
    protected function saveNewDate($date)
    {
        /** @var Mage_Core_Model_Config $config */
        $config = Mage::getModel('core/config');
        /** @var  Dhl_Versenden_Model_Config $modulConfig */
        $modulConfig = $this->config;
        $path = $modulConfig::CONFIG_XML_PATH_CHECKOUT_TRACKING_NEXT_EXEC;
        $config->saveConfig($path, $date, 'default');
    }

    /**
     * @return bool
     */
    protected function isAvailable()
    {
        return $this->config->isTrackingEnabled() && !$this->config->isSandboxModeEnabled();
    }


}
