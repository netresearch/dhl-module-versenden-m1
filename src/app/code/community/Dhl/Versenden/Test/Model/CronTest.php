<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Test_Model_CronTest extends EcomDev_PHPUnit_Test_Case
{
    protected function setUp()
    {
        parent::setUp();

        $writerMock = $this->getModelMock('dhl_versenden/logger_writer', array('log', 'logException'));
        $this->replaceByMock('singleton', 'dhl_versenden/logger_writer', $writerMock);
    }

    /**
     * @test
     * @loadFixture Model_ShipperConfigTest
     * @loadFixture Model_AutoCreateTest
     * @loadFixture Model_AutoCreateFailureTest
     */
    public function shipmentAutoCreateFailure()
    {
        $historyMock = $this->getResourceModelMock('sales/order_status_history', array('save'));
        $this->replaceByMock('resource_model', 'sales/order_status_history', $historyMock);

        $schedule = Mage::getModel('cron/schedule');
        /** @var Dhl_Versenden_Model_Cron $cron */
        $cron = Mage::getModel('dhl_versenden/cron');
        $cron->shipmentAutoCreate($schedule);

        $this->assertEquals(Mage_Cron_Model_Schedule::STATUS_ERROR, $schedule->getStatus());
    }

    /**
     * @test
     * @loadFixture Model_ShipperConfigTest
     * @loadFixture Model_AutoCreateTest
     */
    public function shipmentAutoCreateSuccess()
    {
        $this->markTestIncomplete('Test must create a shipment in order to be recognized as success.');

        $numVersendenOrders = 1;
        $numSuccess = 1;

        $autocreateMock = $this->getModelMock(
            'dhl_versenden/shipping_autocreate',
            array('autoCreate'),
            false,
            array(),
            '',
            false
        );
        $autocreateMock
            ->expects($this->once())
            ->method('autoCreate')
            ->willReturn($numSuccess);
        $this->replaceByMock('model', 'dhl_versenden/shipping_autocreate', $autocreateMock);

        $schedule = Mage::getModel('cron/schedule');

        /** @var Dhl_Versenden_Model_Cron $cron */
        $cron = Mage::getModel('dhl_versenden/cron');
        $cron->shipmentAutoCreate($schedule);

        $format = Dhl_Versenden_Model_Cron::CRON_MESSAGE_LABELS_RETRIEVED;
        $expectedMessage = sprintf($format, $numSuccess, $numVersendenOrders);

        $this->assertEquals(Mage_Cron_Model_Schedule::STATUS_SUCCESS, $schedule->getStatus());
        $this->assertEquals($expectedMessage, $schedule->getMessages());
    }
}
