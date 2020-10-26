<?php

/**
 * See LICENSE.md for license details.
 */

class Dhl_Versenden_Test_Model_Observer_DeleteTrackTest
    extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     */
    public function deleteShippingLabelWrongCarrier()
    {
        $gatewayMock = $this->getModelMock('dhl_versenden/webservice_gateway_soap', array('deleteShipmentOrder'));
        $gatewayMock
            ->expects($this->never())
            ->method('deleteShipmentOrder');
        $this->replaceByMock('model', 'dhl_versenden/webservice_gateway_soap', $gatewayMock);

        $track = Mage::getModel('sales/order_shipment_track');
        $track->setCarrierCode('foo');

        $observer = new Varien_Event_Observer();
        $observer->setData('track', $track);

        $dhlObserver = new Dhl_Versenden_Model_Observer();
        $dhlObserver->deleteShippingLabel($observer);
    }

    /**
     * @test
     */
    public function deleteShippingLabelNoLabel()
    {
        $gatewayMock = $this->getModelMock('dhl_versenden/webservice_gateway_soap', array('deleteShipmentOrder'));
        $gatewayMock
            ->expects($this->never())
            ->method('deleteShipmentOrder');
        $this->replaceByMock('model', 'dhl_versenden/webservice_gateway_soap', $gatewayMock);

        $track = Mage::getModel('sales/order_shipment_track');
        $track->setCarrierCode(Dhl_Versenden_Model_Shipping_Carrier_Versenden::CODE);

        $observer = new Varien_Event_Observer();
        $observer->setData('track', $track);

        $dhlObserver = new Dhl_Versenden_Model_Observer();
        $dhlObserver->deleteShippingLabel($observer);
    }

    /**
     * @test
     * @loadFixture Model_ObserverTest
     * @expectedException Mage_Core_Exception
     */
    public function deleteShippingLabelStatusError()
    {
        $response = new Varien_Object();
        $status = new Dhl\Versenden\Bcs\Api\Webservice\ResponseData\Status\Response(
            '2000',
            'Unknown shipment number.',
            array('Multiple shipments found for cancellation')
        );
        $response->setData('status', $status);

        $gatewayMock = $this->getModelMock('dhl_versenden/webservice_gateway_soap', array('deleteShipmentOrder'));
        $gatewayMock
            ->expects($this->once())
            ->method('deleteShipmentOrder')
            ->willReturn($response);
        $this->replaceByMock('model', 'dhl_versenden/webservice_gateway_soap', $gatewayMock);

        $shipment = Mage::getModel('sales/order_shipment')->load(100);
        $track = Mage::getModel('sales/order_shipment_track');
        $track->setCarrierCode(Dhl_Versenden_Model_Shipping_Carrier_Versenden::CODE);
        $track->setShipment($shipment);

        $observer = new Varien_Event_Observer();
        $observer->setData('track', $track);

        $dhlObserver = new Dhl_Versenden_Model_Observer();
        $dhlObserver->deleteShippingLabel($observer);
    }

    /**
     * @test
     * @loadFixture Model_ObserverTest
     */
    public function deleteShippingLabelOk()
    {
        $response = new Varien_Object();
        $status = new Dhl\Versenden\Bcs\Api\Webservice\ResponseData\Status\Response('0', 'ok', array(''));
        $response->setData('status', $status);

        $gatewayMock = $this->getModelMock('dhl_versenden/webservice_gateway_soap', array('deleteShipmentOrder'));
        $gatewayMock
            ->expects($this->once())
            ->method('deleteShipmentOrder')
            ->willReturn($response);
        $this->replaceByMock('model', 'dhl_versenden/webservice_gateway_soap', $gatewayMock);

        $shipment = Mage::getModel('sales/order_shipment')->load(100);
        $this->assertNotEmpty($shipment->getData('shipping_label'));

        $track = Mage::getModel('sales/order_shipment_track');
        $track->setCarrierCode(Dhl_Versenden_Model_Shipping_Carrier_Versenden::CODE);
        $track->setShipment($shipment);

        $observer = new Varien_Event_Observer();
        $observer->setData('track', $track);

        $dhlObserver = new Dhl_Versenden_Model_Observer();
        $dhlObserver->deleteShippingLabel($observer);

        $this->assertEmpty($shipment->getData('shipping_label'));
    }
}
