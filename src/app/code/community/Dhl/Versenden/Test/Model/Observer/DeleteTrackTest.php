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

/**
 * Dhl_Versenden_Test_Model_Observer_DeleteTrackTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
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
     * @expectedException Mage_Core_Exception
     */
    public function deleteShippingLabelStatusError()
    {
        $response = new Varien_Object();
        $status = new Dhl\Versenden\Webservice\ResponseData\Status\Response(
            '2000',
            'Unknown shipment number.',
            'Multiple shipments found for cancelation'
        );
        $response->setData('status', $status);

        $gatewayMock = $this->getModelMock('dhl_versenden/webservice_gateway_soap', array('deleteShipmentOrder'));
        $gatewayMock
            ->expects($this->once())
            ->method('deleteShipmentOrder')
            ->willReturn($response);
        $this->replaceByMock('model', 'dhl_versenden/webservice_gateway_soap', $gatewayMock);

        $track = Mage::getModel('sales/order_shipment_track');
        $track->setCarrierCode(Dhl_Versenden_Model_Shipping_Carrier_Versenden::CODE);


        $observer = new Varien_Event_Observer();
        $observer->setData('track', $track);

        $dhlObserver = new Dhl_Versenden_Model_Observer();
        $dhlObserver->deleteShippingLabel($observer);
    }
}
