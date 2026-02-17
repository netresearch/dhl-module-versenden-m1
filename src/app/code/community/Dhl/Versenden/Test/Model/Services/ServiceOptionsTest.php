<?php

/**
 * See LICENSE.md for license details.
 */

/**
 * Test Services ServiceOptions functionality.
 *
 * Tests the conversion of API service availability responses to
 * checkout dropdown options.
 */
class Dhl_Versenden_Test_Model_Services_ServiceOptionsTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Test getOptions returns formatted date options for PreferredDayAvailable.
     *
     * @test
     */
    public function getOptionsReturnsDateOptionsForPreferredDay()
    {
        $serviceOptions = new Dhl_Versenden_Model_Services_ServiceOptions();

        // Create mock valid days with DateTime end dates
        $day1End = new DateTime('2025-01-15');
        $day2End = new DateTime('2025-01-16');

        $day1Mock = $this->getMockBuilder('stdClass')
            ->addMethods(['getEnd'])
            ->getMock();
        $day1Mock->method('getEnd')->willReturn($day1End);

        $day2Mock = $this->getMockBuilder('stdClass')
            ->addMethods(['getEnd'])
            ->getMock();
        $day2Mock->method('getEnd')->willReturn($day2End);

        // Mock PreferredDayAvailable service model
        $serviceModelMock = $this->getMockBuilder('stdClass')
            ->addMethods(['getModelName', 'getValidDays'])
            ->getMock();
        $serviceModelMock->method('getModelName')->willReturn('PreferredDayAvailable');
        $serviceModelMock->method('getValidDays')->willReturn([$day1Mock, $day2Mock]);

        $options = $serviceOptions->getOptions($serviceModelMock);

        // Should return array with date keys
        static::assertIsArray($options);
        static::assertArrayHasKey('2025-01-15', $options);
        static::assertArrayHasKey('2025-01-16', $options);

        // Each option should have value and disabled keys
        static::assertArrayHasKey('value', $options['2025-01-15']);
        static::assertArrayHasKey('disabled', $options['2025-01-15']);
        static::assertFalse($options['2025-01-15']['disabled']);
    }

    /**
     * Test getOptions returns empty array for unknown service model.
     *
     * @test
     */
    public function getOptionsReturnsEmptyArrayForUnknownService()
    {
        $serviceOptions = new Dhl_Versenden_Model_Services_ServiceOptions();

        // Mock unknown service model
        $serviceModelMock = $this->getMockBuilder('stdClass')
            ->addMethods(['getModelName'])
            ->getMock();
        $serviceModelMock->method('getModelName')->willReturn('UnknownService');

        $options = $serviceOptions->getOptions($serviceModelMock);

        static::assertIsArray($options);
        static::assertEmpty($options);
    }

    /**
     * Test getOptions handles empty valid days for PreferredDayAvailable.
     *
     * @test
     */
    public function getOptionsReturnsEmptyArrayForEmptyValidDays()
    {
        $serviceOptions = new Dhl_Versenden_Model_Services_ServiceOptions();

        // Mock PreferredDayAvailable with no valid days
        $serviceModelMock = $this->getMockBuilder('stdClass')
            ->addMethods(['getModelName', 'getValidDays'])
            ->getMock();
        $serviceModelMock->method('getModelName')->willReturn('PreferredDayAvailable');
        $serviceModelMock->method('getValidDays')->willReturn([]);

        $options = $serviceOptions->getOptions($serviceModelMock);

        static::assertIsArray($options);
        static::assertEmpty($options);
    }

}
