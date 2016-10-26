<?php

/**
 * @author   MArcus at Localhosrt
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     https://gist.github.com/marcus-at-localhorst/409f94440cf59e695eac
 */
class Mal_Holidays
{
    public static function getEaster()
    {
        $easter = new DateTime('now');
        $year   = $easter->format('Y');
        $easter->setDate($year, 3, 21);
        $easter->setTime(0, 0, 0);
        $easter->modify('+' . easter_days($year) . 'days');

        return $easter;
    }

    public static function holidays()
    {
        // removed all non static holidays
        return array(
            "neujahr"                   => new DateTime('jan 1st'),
            "karfreitag"                => self::getEaster()->modify('-2 days'),
            "ostermontag"               => self::getEaster()->modify('+1 day'),
            "tag_der_arbeit"            => new DateTime('may 1st'),
            "christi_himmelfahrt"       => self::getEaster()->modify('+39 days'),
            "pfingstmontag"             => self::getEaster()->modify('+50 days'),
            "tag_der_deutschen_einheit" => new DateTime('oct 3'),
            "weihnachtstag1"            => new DateTime('dec 25th'),
            "weihnachtstag2"            => new DateTime('dec 26th'),
        );
    }

    /**
     * Check if this is a holiday
     *
     * @param  string|DateTime object $date accepts valid date/time string or valid date object
     *
     * @return boolean
     */
    public static function isHoliday($date = 'now')
    {
        $holidays = self::holidays();

        // create object in case of a time string
        if (is_string($date)) {
            $date = new  DateTime($date);
        }
        $date->setTime(0, 0, 0);

        foreach ($holidays as $holy => $day) {
            // returns 0 if date is the same or just compare
            // http://php.net/manual/en/datetime.diff.php
            if ($date == $day) {
                return true;
            }
        }

        return false;
    }
}
