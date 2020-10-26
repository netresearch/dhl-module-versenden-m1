<?php

/**
 * See LICENSE.md for license details.
 */

use \Dhl\Versenden\Bcs\Api\Webservice\RequestData;

class Dhl_Versenden_Test_Model_Webservice_RequestData_ShipmentOrderTest
    extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     * @dataProvider Dhl_Versenden_Test_Provider_ShipmentOrder::provider()
     *
     * @param RequestData\ShipmentOrder $shipmentOrder
     * @param Dhl_Versenden_Test_Expectation_ShipmentOrder $expectation
     */
    public function shipmentOrderCollection(
        RequestData\ShipmentOrder $shipmentOrder,
        Dhl_Versenden_Test_Expectation_ShipmentOrder $expectation
    ) {
        $collection = new RequestData\ShipmentOrderCollection();
        $this->assertCount(0, $collection);

        $collection->addItem($shipmentOrder);
        $this->assertCount(1, $collection);

        $item = $collection->getItem($expectation->getSequenceNumber());
        $this->assertSame($shipmentOrder, $item);

        $item = $collection->getItem('foo');
        $this->assertNull($item);

        $collection->setItems(array($shipmentOrder));
        $this->assertCount(1, $collection);

        $items = $collection->getItems();
        $this->assertCount(1, $items);
        foreach ($collection as $sequenceNumber => $item) {
            $this->assertSame($expectation->getSequenceNumber(), $sequenceNumber);
            $this->assertSame($shipmentOrder, $item);
        }
    }
}
