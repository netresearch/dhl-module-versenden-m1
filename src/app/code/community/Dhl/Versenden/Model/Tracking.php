<?php

/**
 * See LICENSE.md for license details.
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

    /**
     * @var string
     */
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
            $this->config->setNextTrackDate($nextDate);
            return true;
        }

        return false;
    }

    /**
     * @param $date
     * @return bool|string
     */
    protected function checkInterval($date)
    {
        try {
            $dateTime = new DateTime($date);
            $interval = $this->config->getTrackingInterval();
            $tmpDate = $dateTime->add(new DateInterval("P{$interval}D"));
            $nextTrackConfig = $this->config->getNextTrackDate();
            $nextPossibleDay = $tmpDate->format($this->timeformat);
            if (!$nextTrackConfig || $date === $nextTrackConfig) {
                return $nextPossibleDay;
            }
            
            return false;
        } catch(Exception $e) {
            return false;
        }
    }

    /**
     * @return bool
     */
    protected function isAvailable()
    {
        return $this->config->isTrackingEnabled() && !$this->config->isSandboxModeEnabled();
    }
}
