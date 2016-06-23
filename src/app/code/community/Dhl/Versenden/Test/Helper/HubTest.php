<?php
/**
 * DeutschePost Internetmarke
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
 * @category  DeutschePost
 * @package   DeutschePost_Internetmarke
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */

/**
 * DeutschePost_Internetmarke_Test_Helper_HubTest
 *
 * @category DeutschePost
 * @package  DeutschePost_Internetmarke
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class DeutschePost_Internetmarke_Test_Helper_HubTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * this module's order helper is only a placeholder for a concrete
     * implementation in an API module, doing nothing…
     *
     * @test
     */
    public function orderHelper()
    {
        $helper = Mage::helper('deutschepost_internetmarke/order');
        $soi = Mage::getModel('deutschepost_internetmarke/shipping_order_item');

        $this->assertNull($helper->persistShippingOrderItem($soi));

        $trackId = 'XX999999999DE';
        $this->assertEmpty($helper->getTrackingLink($trackId));
    }

    /**
     * this module's product helper is only a placeholder for a concrete
     * implementation in an API module, doing nothing…
     *
     * @test
     */
    public function productHelper()
    {
        $helper = Mage::helper('deutschepost_internetmarke/product');

        $productNames = $helper->getProductNames(33, array('TR808'));
        $this->assertInternalType('array', $productNames);
        $this->assertEmpty($productNames);

        $products = $helper->getAvailableProducts('CZ');
        $this->assertInternalType('array', $products);
        $this->assertEmpty($products);

        $services = $helper->getAvailableServices('CZ');
        $this->assertInternalType('array', $services);
        $this->assertEmpty($services);

        $soi = $helper->initShippingOrderItem(1234, array(), 'CZ');
        $this->assertNull($soi);
    }

    /**
     * @test
     */
    public function initShippingOrderItem()
    {
        // reset helper first
        $registryKey = '_helper/deutschepost_internetmarke/hub';
        Mage::unregister($registryKey);

        $shipmentId = 101;
        $productId  = 202;
        $serviceIds = array(303, 404);
        $countryId  = 'AT';
        $createdAtDate = '2015-07-01';
        $address = Mage::getModel('sales/order_address');
        $address->setCountryId($countryId);

        $shipment = new Varien_Object();
        $shipment->setData(array(
            'id' => $shipmentId,
            'dpim_product' => $productId,
            'dpim_service' => $serviceIds,
            'shipping_address' => $address,
            'created_at_date' => $createdAtDate,
        ));

        $request = Mage::getModel('shipping/shipment_request');
        $request->setOrderShipment($shipment);

        $soi = Mage::getModel('deutschepost_internetmarke/shipping_order_item');
        $productHelperMock = $this->getHelperMock(
            'deutschepost_internetmarke/product',
            array('initShippingOrderItem')
        );
        $productHelperMock
            ->expects($this->once())
            ->method('initShippingOrderItem')
            ->willReturn($soi)
        ;

        $configMock = $this->getModelMock(
            'deutschepost_internetmarke/config',
            array('getProductHelper')
        );
        $configMock
            ->expects($this->once())
            ->method('getProductHelper')
            ->willReturn($productHelperMock)
        ;
        $this->replaceByMock('model', 'deutschepost_internetmarke/config', $configMock);

        $helper = Mage::helper('deutschepost_internetmarke/hub');
        $soi = $helper->initShippingOrderItem($request);
        $this->assertSame($shipmentId, $soi->getShipmentId());
    }
}
