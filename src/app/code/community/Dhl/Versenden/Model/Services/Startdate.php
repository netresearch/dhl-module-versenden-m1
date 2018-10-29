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
 * @category  Dhl
 * @package   Dhl_Versenden
 * @author    Andreas Müller <andreas.mueller@netresearch.de>
 * @copyright 2018 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */

/**
 * Dhl_Versenden_Model_Services_Startdate
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Andreas Müller <andreas.mueller@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
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
            $startdate = $date;
        } else {
            $end = 2;
            for ($i = 1; $i < $end; $i++) {
                if ($i > 7) {
                    Mage::throwException('No valid start date found within next week.');
                }

                $datetime = new DateTime($date);
                $tmpDate = $datetime->add(new DateInterval("P{$i}D"));
                $nextPossibleDay = $tmpDate->format($timeformat);
                $isAvailble = $this->isAvailable($nextPossibleDay, $noDropOffDays);
                $isAvailble ? $end-- : $end++;
            }

            $startdate = $nextPossibleDay;
        }

        return $startdate;
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

