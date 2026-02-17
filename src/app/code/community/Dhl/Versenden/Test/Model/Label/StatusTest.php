<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Test_Model_Label_StatusTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     */
    public function setLabelDeletedUpdatesStatusOnSuccess()
    {
        $order = new Mage_Sales_Model_Order();
        $order->setId(123);

        // Mock successful deletion item
        $deletedItem = $this->getMockBuilder('stdClass')
            ->addMethods(['isError'])
            ->getMock();
        $deletedItem->method('isError')->willReturn(false);

        $labelStatus = new Dhl_Versenden_Model_Label_Status();
        $labelStatus->setLabelDeleted($order, $deletedItem);

        static::assertEquals(123, $labelStatus->getOrderId());
        static::assertEquals(
            Dhl_Versenden_Model_Label_Status::CODE_PENDING,
            $labelStatus->getStatusCode(),
        );
    }

    /**
     * @test
     */
    public function setLabelDeletedDoesNotUpdateStatusOnError()
    {
        $order = new Mage_Sales_Model_Order();
        $order->setId(456);

        // Mock failed deletion item
        $deletedItem = $this->getMockBuilder('stdClass')
            ->addMethods(['isError'])
            ->getMock();
        $deletedItem->method('isError')->willReturn(true);

        $labelStatus = new Dhl_Versenden_Model_Label_Status();
        // Set an existing status that should remain unchanged
        $labelStatus->setStatusCode(Dhl_Versenden_Model_Label_Status::CODE_PROCESSED);
        $labelStatus->setLabelDeleted($order, $deletedItem);

        // Order ID should be set
        static::assertEquals(456, $labelStatus->getOrderId());
        // Status should remain PROCESSED (not changed to PENDING)
        static::assertEquals(
            Dhl_Versenden_Model_Label_Status::CODE_PROCESSED,
            $labelStatus->getStatusCode(),
        );
    }

    /**
     * @test
     */
    public function setLabelCreatedUpdatesStatusOnSuccess()
    {
        $order = new Mage_Sales_Model_Order();
        $order->setId(789);

        // Mock successful creation item
        $createdItem = $this->getMockBuilder('stdClass')
            ->addMethods(['getShipmentNumber'])
            ->getMock();
        $createdItem->method('getShipmentNumber')->willReturn('12345678901');

        $labelStatus = new Dhl_Versenden_Model_Label_Status();
        $labelStatus->setLabelCreated($order, $createdItem);

        static::assertEquals(789, $labelStatus->getOrderId());
        static::assertEquals(
            Dhl_Versenden_Model_Label_Status::CODE_PROCESSED,
            $labelStatus->getStatusCode(),
        );
    }

    /**
     * @test
     */
    public function setLabelCreatedUpdatesStatusOnFailure()
    {
        $order = new Mage_Sales_Model_Order();
        $order->setId(101);

        // Null creation item indicates failure
        $labelStatus = new Dhl_Versenden_Model_Label_Status();
        $labelStatus->setLabelCreated($order, null);

        static::assertEquals(101, $labelStatus->getOrderId());
        static::assertEquals(
            Dhl_Versenden_Model_Label_Status::CODE_FAILED,
            $labelStatus->getStatusCode(),
        );
    }

    /**
     * @test
     */
    public function gettersAndSettersWorkCorrectly()
    {
        $labelStatus = new Dhl_Versenden_Model_Label_Status();

        $labelStatus->setOrderId(999);
        static::assertEquals(999, $labelStatus->getOrderId());

        $labelStatus->setStatusCode(Dhl_Versenden_Model_Label_Status::CODE_FAILED);
        static::assertEquals(
            Dhl_Versenden_Model_Label_Status::CODE_FAILED,
            $labelStatus->getStatusCode(),
        );
    }

    /**
     * @test
     */
    public function constantsAreCorrectlyDefined()
    {
        static::assertEquals('order_id', Dhl_Versenden_Model_Label_Status::FIELD_ORDER_ID);
        static::assertEquals('status_code', Dhl_Versenden_Model_Label_Status::FIELD_STATUS_CODE);
        static::assertEquals(0, Dhl_Versenden_Model_Label_Status::CODE_OTHER);
        static::assertEquals(10, Dhl_Versenden_Model_Label_Status::CODE_PENDING);
        static::assertEquals(20, Dhl_Versenden_Model_Label_Status::CODE_PROCESSED);
        static::assertEquals(30, Dhl_Versenden_Model_Label_Status::CODE_FAILED);
    }
}
