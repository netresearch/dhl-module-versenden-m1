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
 * PHP version 5
 *
 * @category  Dhl
 * @package   Dhl_Versenden
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
use \Netresearch\Dhl\Versenden\Webservice\RequestData;
/**
 * Dhl_Versenden_Test_Model_Webservice_RequestData_CreateShipment_ShipmentOrderTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
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
