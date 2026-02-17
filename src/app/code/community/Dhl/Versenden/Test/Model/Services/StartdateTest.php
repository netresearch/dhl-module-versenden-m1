<?php

/**
 * See LICENSE.md for license details.
 */

/**
 * Test Services Startdate functionality.
 *
 * Tests the start date calculation for DHL shipments based on
 * cutoff times and drop-off day restrictions.
 */
class Dhl_Versenden_Test_Model_Services_StartdateTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Test getStartdate returns same date when within cutoff and available.
     *
     * @test
     */
    public function getStartdateReturnsSameDateWhenWithinCutoffAndAvailable()
    {
        // Mock date model - use a Wednesday (day 3)
        $dateModelMock = $this->getMockBuilder(Mage_Core_Model_Date::class)
            ->setMethods(['gmtDate', 'gmtTimestamp'])
            ->getMock();
        // 2025-01-08 is a Wednesday
        $dateModelMock->method('gmtDate')
            ->with('w', '2025-01-08 10:00:00')
            ->willReturn('3');
        $dateModelMock->method('gmtTimestamp')
            ->willReturn(strtotime('2025-01-08 10:00:00'));
        $this->replaceByMock('singleton', 'core/date', $dateModelMock);

        $startdate = new Dhl_Versenden_Model_Services_Startdate();

        // Cutoff time is later than the date (still in cutoff window)
        $cutOffTime = strtotime('2025-01-08 14:00:00');
        $result = $startdate->getStartdate('2025-01-08 10:00:00', $cutOffTime, '');

        static::assertEquals('2025-01-08 10:00:00', $result);
    }

    /**
     * Test getStartdate returns next day when past cutoff time.
     *
     * @test
     */
    public function getStartdateReturnsNextDayWhenPastCutoff()
    {
        // Mock date model
        $dateModelMock = $this->getMockBuilder(Mage_Core_Model_Date::class)
            ->setMethods(['gmtDate', 'gmtTimestamp'])
            ->getMock();
        // Return appropriate weekday for each date checked
        $dateModelMock->method('gmtDate')
            ->willReturnCallback(function ($format, $date) {
                return date($format, strtotime($date));
            });
        $dateModelMock->method('gmtTimestamp')
            ->willReturn(strtotime('2025-01-08 16:00:00'));
        $this->replaceByMock('singleton', 'core/date', $dateModelMock);

        $startdate = new Dhl_Versenden_Model_Services_Startdate();

        // Cutoff time has passed
        $cutOffTime = strtotime('2025-01-08 14:00:00');
        $result = $startdate->getStartdate('2025-01-08 16:00:00', $cutOffTime, '');

        // Should return next day (Thursday)
        static::assertStringContainsString('2025-01-09', $result);
    }

    /**
     * Test getStartdate skips Sundays.
     *
     * @test
     */
    public function getStartdateSkipsSundays()
    {
        // Mock date model
        $dateModelMock = $this->getMockBuilder(Mage_Core_Model_Date::class)
            ->setMethods(['gmtDate', 'gmtTimestamp'])
            ->getMock();
        $dateModelMock->method('gmtDate')
            ->willReturnCallback(function ($format, $date) {
                return date($format, strtotime($date));
            });
        // Force past cutoff
        $dateModelMock->method('gmtTimestamp')
            ->willReturn(strtotime('2025-01-11 16:00:00'));
        $this->replaceByMock('singleton', 'core/date', $dateModelMock);

        $startdate = new Dhl_Versenden_Model_Services_Startdate();

        // Saturday 2025-01-11, cutoff passed, next day is Sunday (skipped)
        $cutOffTime = strtotime('2025-01-11 14:00:00');
        $result = $startdate->getStartdate('2025-01-11 16:00:00', $cutOffTime, '');

        // Should skip Sunday (2025-01-12) and return Monday (2025-01-13)
        static::assertStringContainsString('2025-01-13', $result);
    }

    /**
     * Test getStartdate skips configured no-drop-off days.
     *
     * @test
     */
    public function getStartdateSkipsNoDropOffDays()
    {
        // Mock date model
        $dateModelMock = $this->getMockBuilder(Mage_Core_Model_Date::class)
            ->setMethods(['gmtDate', 'gmtTimestamp'])
            ->getMock();
        $dateModelMock->method('gmtDate')
            ->willReturnCallback(function ($format, $date) {
                return date($format, strtotime($date));
            });
        $dateModelMock->method('gmtTimestamp')
            ->willReturn(strtotime('2025-01-08 16:00:00'));
        $this->replaceByMock('singleton', 'core/date', $dateModelMock);

        $startdate = new Dhl_Versenden_Model_Services_Startdate();

        // Wednesday past cutoff, skip Thursday (4) and Friday (5)
        $cutOffTime = strtotime('2025-01-08 14:00:00');
        $result = $startdate->getStartdate('2025-01-08 16:00:00', $cutOffTime, '4,5');

        // Should skip Thursday 9, Friday 10, Sunday 12, return Saturday 11 or Monday 13
        // Actually Saturday (6) is allowed, so it should be 2025-01-11
        static::assertStringContainsString('2025-01-11', $result);
    }

    /**
     * Test getStartdate handles null noDropOffDays parameter.
     *
     * @test
     */
    public function getStartdateHandlesNullNoDropOffDays()
    {
        // Mock date model
        $dateModelMock = $this->getMockBuilder(Mage_Core_Model_Date::class)
            ->setMethods(['gmtDate', 'gmtTimestamp'])
            ->getMock();
        $dateModelMock->method('gmtDate')
            ->willReturnCallback(function ($format, $date) {
                return date($format, strtotime($date));
            });
        $dateModelMock->method('gmtTimestamp')
            ->willReturn(strtotime('2025-01-08 10:00:00'));
        $this->replaceByMock('singleton', 'core/date', $dateModelMock);

        $startdate = new Dhl_Versenden_Model_Services_Startdate();

        // Cutoff time is later (still in cutoff window), null noDropOffDays
        $cutOffTime = strtotime('2025-01-08 14:00:00');

        // Should not throw an error with null
        $result = $startdate->getStartdate('2025-01-08 10:00:00', $cutOffTime, null);

        static::assertEquals('2025-01-08 10:00:00', $result);
    }

    /**
     * Test getStartdate handles empty string noDropOffDays parameter.
     *
     * @test
     */
    public function getStartdateHandlesEmptyNoDropOffDays()
    {
        // Mock date model
        $dateModelMock = $this->getMockBuilder(Mage_Core_Model_Date::class)
            ->setMethods(['gmtDate', 'gmtTimestamp'])
            ->getMock();
        $dateModelMock->method('gmtDate')
            ->willReturnCallback(function ($format, $date) {
                return date($format, strtotime($date));
            });
        $dateModelMock->method('gmtTimestamp')
            ->willReturn(strtotime('2025-01-08 10:00:00'));
        $this->replaceByMock('singleton', 'core/date', $dateModelMock);

        $startdate = new Dhl_Versenden_Model_Services_Startdate();

        $cutOffTime = strtotime('2025-01-08 14:00:00');

        // Should not throw an error with empty string
        $result = $startdate->getStartdate('2025-01-08 10:00:00', $cutOffTime, '');

        static::assertEquals('2025-01-08 10:00:00', $result);
    }

    /**
     * Test getStartdate throws exception when no valid date within week.
     *
     * @test
     */
    public function getStartdateThrowsExceptionWhenNoValidDateInWeek()
    {
        $this->expectException(Mage_Core_Exception::class);
        $this->expectExceptionMessage('No valid start date found within next week.');

        // Mock date model - always return Sunday
        $dateModelMock = $this->getMockBuilder(Mage_Core_Model_Date::class)
            ->setMethods(['gmtDate', 'gmtTimestamp'])
            ->getMock();
        // Always return Sunday (0) to force all days to be unavailable
        $dateModelMock->method('gmtDate')
            ->willReturn('0');
        // Force past cutoff
        $dateModelMock->method('gmtTimestamp')
            ->willReturn(strtotime('2025-01-05 16:00:00'));
        $this->replaceByMock('singleton', 'core/date', $dateModelMock);

        $startdate = new Dhl_Versenden_Model_Services_Startdate();

        // Cutoff passed, all days return Sunday
        $cutOffTime = strtotime('2025-01-05 14:00:00');
        $startdate->getStartdate('2025-01-05 16:00:00', $cutOffTime, '');
    }

    /**
     * Test getStartdate finds next Monday when starting from weekend.
     *
     * @test
     */
    public function getStartdateFindsNextMondayFromWeekend()
    {
        // Mock date model
        $dateModelMock = $this->getMockBuilder(Mage_Core_Model_Date::class)
            ->setMethods(['gmtDate', 'gmtTimestamp'])
            ->getMock();
        $dateModelMock->method('gmtDate')
            ->willReturnCallback(function ($format, $date) {
                return date($format, strtotime($date));
            });
        // Force past cutoff
        $dateModelMock->method('gmtTimestamp')
            ->willReturn(strtotime('2025-01-04 16:00:00'));
        $this->replaceByMock('singleton', 'core/date', $dateModelMock);

        $startdate = new Dhl_Versenden_Model_Services_Startdate();

        // Saturday 2025-01-04 past cutoff, Sunday is skipped
        $cutOffTime = strtotime('2025-01-04 14:00:00');
        $result = $startdate->getStartdate('2025-01-04 16:00:00', $cutOffTime, '6');

        // Saturday (6) is no-dropoff, Sunday (0) is always skipped, should return Monday
        static::assertStringContainsString('2025-01-06', $result);
    }
}
