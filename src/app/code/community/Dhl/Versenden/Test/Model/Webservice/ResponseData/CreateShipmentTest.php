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
use \Dhl\Versenden\Bcs\Api\Webservice\ResponseData;
/**
 * Dhl_Versenden_Test_Model_Webservice_ResponseData_CreateShipmentTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Test_Model_Webservice_ResponseData_CreateShipmentTest
    extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     */
    public function createShipment()
    {
        $statusCode = '0';
        $statusText = 'ok';
        $statusMessage = 'Foo Message';

        $sequenceNumber = '1000';
        $shipmentNumber = '0001';

        $status = new ResponseData\Status\Item($shipmentNumber, $statusCode, $statusText, array($statusMessage));

        $defaultLabel = new Zend_Pdf();
        $defaultLabel->pages[]= new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);
        $defaultLabelData = $defaultLabel->render();

        $returnLabel = new Zend_Pdf();
        $returnLabel->pages[]= new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);
        $returnLabelData = $defaultLabel->render();

        $label = new ResponseData\CreateShipment\Label($status, $shipmentNumber, $defaultLabelData, $returnLabelData);

        $sequence = array($sequenceNumber => $shipmentNumber);


        $labelCollection = new ResponseData\CreateShipment\LabelCollection();
        $this->assertCount(0, $labelCollection);

        $labelCollection->addItem($label);
        $this->assertCount(1, $labelCollection);

        $item = $labelCollection->getItem($shipmentNumber);
        $this->assertInstanceOf(ResponseData\CreateShipment\Label::class, $item);
        $item = $labelCollection->getItem('foo');
        $this->assertNull($item);

        $labelCollection->setItems(array($label));
        $this->assertCount(1, $labelCollection->getItems());

        /** @var ResponseData\CreateShipment\Label $item */
        foreach ($labelCollection as $idx => $item) {
            $this->assertEquals($shipmentNumber, $idx);
            $this->assertSame($defaultLabelData, $item->getLabel());
            $this->assertSame($returnLabelData, $item->getReturnLabel());
            $allLabels = $item->getAllLabels(new \Dhl\Versenden\Bcs\Api\Pdf\Adapter\Zend());
            $allLabelsPdf = Zend_Pdf::parse($allLabels);
            $this->assertCount(2, $allLabelsPdf->pages);
        }

        $createShipment = new ResponseData\CreateShipment($status, $labelCollection, $sequence);

        $this->assertTrue($createShipment->getStatus()->isSuccess());
        $this->assertFalse($createShipment->getStatus()->isError());

        $this->assertSame($statusCode, $createShipment->getStatus()->getStatusCode());
        $this->assertSame($statusText, $createShipment->getStatus()->getStatusText());
        $this->assertSame($statusMessage, $createShipment->getStatus()->getStatusMessage());

        $this->assertSame($sequence, $createShipment->getShipmentNumbers());
        $this->assertEquals($shipmentNumber, $createShipment->getShipmentNumber($sequenceNumber));
        $this->assertNull($createShipment->getShipmentNumber('9999'));

        $this->assertTrue($createShipment->getCreatedItems()->getItem($shipmentNumber)->isCreated());
        $this->assertSame($defaultLabelData, $createShipment->getCreatedItems()->getItem($shipmentNumber)->getLabel());
        $this->assertSame($returnLabelData, $createShipment->getCreatedItems()->getItem($shipmentNumber)->getReturnLabel());
        $this->assertEmpty($createShipment->getCreatedItems()->getItem($shipmentNumber)->getExportLabel());
        $this->assertEmpty($createShipment->getCreatedItems()->getItem($shipmentNumber)->getCodLabel());
    }
}
