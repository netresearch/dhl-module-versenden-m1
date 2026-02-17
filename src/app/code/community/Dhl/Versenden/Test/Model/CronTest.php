<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Test_Model_CronTest extends EcomDev_PHPUnit_Test_Case
{
    protected function setUp(): void
    {
        parent::setUp();

        $writerMock = $this->getModelMock('dhl_versenden/logger_writer', ['log', 'logException']);
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
        $historyMock = $this->getResourceModelMock('sales/order_status_history', ['save']);
        $this->replaceByMock('resource_model', 'sales/order_status_history', $historyMock);

        $schedule = Mage::getModel('cron/schedule');
        /** @var Dhl_Versenden_Model_Cron $cron */
        $cron = Mage::getModel('dhl_versenden/cron');
        $cron->shipmentAutoCreate($schedule);

        static::assertEquals(Mage_Cron_Model_Schedule::STATUS_ERROR, $schedule->getStatus());
    }

    /**
     * @test
     * @loadFixture Model_ShipperConfigTest
     * @loadFixture Model_AutoCreateTest
     */
    public function shipmentAutoCreateSuccess()
    {
        $numVersendenOrders = 1;
        $numSuccess = 1;

        // Create a mock order with shipment to satisfy hasShipments() check
        $shipment = Mage::getModel('sales/order_shipment');
        $orderMock = $this->getModelMock('sales/order', ['hasShipments']);
        $orderMock
            ->expects(static::any())
            ->method('hasShipments')
            ->willReturn(true); // This makes the order pass the "failed orders" check

        // Mock the autocreate collection to return the order with shipment
        $collectionMock = $this->getResourceModelMock('dhl_versenden/autocreate_collection', [
            'addShippingMethodFilter',
            'addShipmentFilter',
            'addDeliveryCountriesFilter',
            'addStatusFilter',
            'addStoreFilter',
            'setPageSize',
            'getSize',
            'getItems',
        ]);
        $collectionMock
            ->expects(static::any())
            ->method('addShippingMethodFilter')
            ->willReturnSelf();
        $collectionMock
            ->expects(static::any())
            ->method('addShipmentFilter')
            ->willReturnSelf();
        $collectionMock
            ->expects(static::any())
            ->method('addDeliveryCountriesFilter')
            ->willReturnSelf();
        $collectionMock
            ->expects(static::any())
            ->method('addStatusFilter')
            ->willReturnSelf();
        $collectionMock
            ->expects(static::any())
            ->method('addStoreFilter')
            ->willReturnSelf();
        $collectionMock
            ->expects(static::any())
            ->method('setPageSize')
            ->willReturnSelf();
        $collectionMock
            ->expects(static::any())
            ->method('getSize')
            ->willReturn($numVersendenOrders);
        $collectionMock
            ->expects(static::any())
            ->method('getItems')
            ->willReturn([$orderMock]);
        $this->replaceByMock('resource_model', 'dhl_versenden/autocreate_collection', $collectionMock);

        $autocreateMock = $this->getModelMock(
            'dhl_versenden/shipping_autocreate',
            ['autoCreate'],
            false,
            [],
            '',
            false,
        );
        $autocreateMock
            ->expects(static::once())
            ->method('autoCreate')
            ->willReturn($numSuccess);
        $this->replaceByMock('model', 'dhl_versenden/shipping_autocreate', $autocreateMock);

        $schedule = Mage::getModel('cron/schedule');

        /** @var Dhl_Versenden_Model_Cron $cron */
        $cron = Mage::getModel('dhl_versenden/cron');
        $cron->shipmentAutoCreate($schedule);

        $format = Dhl_Versenden_Model_Cron::CRON_MESSAGE_LABELS_RETRIEVED;
        $expectedMessage = sprintf($format, $numSuccess, $numVersendenOrders);

        static::assertEquals(Mage_Cron_Model_Schedule::STATUS_SUCCESS, $schedule->getStatus());
        static::assertEquals($expectedMessage, $schedule->getMessages());
    }
}
