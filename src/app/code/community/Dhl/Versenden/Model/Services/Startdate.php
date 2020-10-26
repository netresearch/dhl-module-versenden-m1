<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Model_Services_Startdate
{
    /**
     * @var Mage_Core_Model_Date
     */
    private $dateModel;

    public function __construct()
    {
        $this->dateModel = Mage::getSingleton('core/date');
    }

    /**
     * @param string $date
     * @param string $cutOffTime
     * @param string $noDropOffDays
     * @return string
     * @throws Exception
     */
    public function getStartdate($date, $cutOffTime, $noDropOffDays)
    {
        $timeformat = 'Y-m-d H:i:s';
        $isInCt = $this->isInCutOffTime($date, $cutOffTime);
        $isWdAv = $this->isAvailable($date, $noDropOffDays);

        if ($isInCt && $isWdAv) {
            return $date;
        }

        for ($i = 1; $i < 8; $i++) {
            $datetime = new DateTime($date);
            $tmpDate = $datetime->add(new DateInterval("P{$i}D"));
            $nextPossibleDay = $tmpDate->format($timeformat);
            if ($this->isAvailable($nextPossibleDay, $noDropOffDays)) {
                return $nextPossibleDay;
            }
        }

        Mage::throwException('No valid start date found within next week.');
    }

    /**
     * @param int|string $date
     * @param string $noDropOffDays
     * @return bool
     */
    protected function isAvailable($date, $noDropOffDays)
    {

        $holidayCheck = new Mal_Holidays();
        $weekday = (int) $this->dateModel->gmtDate('w', $date);
        $isSunday = $weekday === 0;
        $isHoliday = $holidayCheck::isHoliday($date);
        $isDropoffDay = $this->isDropOffDay($weekday, $noDropOffDays);

        return !$isSunday && !$isHoliday && $isDropoffDay;
    }

    /**
     * @param string $date
     * @param string $cutOffTime
     * @return bool
     */
    protected function isInCutOffTime($date, $cutOffTime)
    {
        return $cutOffTime > $this->dateModel->gmtTimestamp($date);
    }

    /**
     * @param int $weekday
     * @param string $noDropOffDays
     * @return bool
     */
    protected function isDropOffDay($weekday, $noDropOffDays)
    {
        $noDropOffDayArray = explode(',', $noDropOffDays);

        return !in_array((string)$weekday, $noDropOffDayArray, true);
    }
}

