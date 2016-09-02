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
use \Dhl\Versenden\Webservice\RequestData\ShipmentOrder\Export;
/**
 * Dhl_Versenden_Test_Model_Webservice_Builder_CustomsTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Test_Model_Webservice_Builder_CustomsTest
    extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     */
    public function getExportDocuments()
    {
        $invoiceNumber = '103000002';

        $packageSequenceNumber = '303';
        $itemSequenceNumberOne = '808';
        $itemSequenceNumberTwo = '909';
        $packageWeight = 2.0000;

        $packageItemOne = array(
            'qty' => 2,
            'weight' => 0.4000,
            'customs_value' => 9.95,
            'customs' => array(
                'description' => 'one',
                'country_of_origin' => 'TR',
                'tariff_number' => '101',
            ),
        );
        $packageItemTwo = array(
            'qty' => 1,
            'weight' => 1.2000,
            'customs_value' => 19.95,
            'customs' => array(
                'description' => 'two',
                'country_of_origin' => 'DE',
                'tariff_number' => '202',
            ),
        );

        $package = array(
            'params' => array(
                'weight' => $packageWeight,
                'content_type' => Dhl_Versenden_Model_Shipping_Carrier_Versenden::EXPORT_TYPE_OTHER,
                'content_type_other' => 'Foo'
            ),
            'items' => array(
                $itemSequenceNumberOne => $packageItemOne,
                $itemSequenceNumberTwo => $packageItemTwo,
            ),
        );

        $customsInfo = array(
            'terms_of_trade' => Dhl_Versenden_Model_Shipping_Carrier_Versenden::TOT_DDX,
            'additional_fee' => 2,
            'place_of_commital' => 'LE',
            'permit_number' => '123',
            'attestation_number' => '456',
            'export_notification' => true,
        );
        $packageInfo = array($packageSequenceNumber => $package);

        $builder = Mage::getModel('dhl_versenden/webservice_builder_customs');

        $documents = $builder->getExportDocuments($invoiceNumber, $customsInfo, $packageInfo);
        $this->assertInstanceOf(Export\DocumentCollection::class, $documents);
        $this->assertCount(1, $documents);

        $document = $documents->getItem($packageSequenceNumber);
        $this->assertInstanceOf(Export\Document::class, $document);
        $this->assertEquals($packageSequenceNumber, $document->getPackageId());
        $this->assertEquals($invoiceNumber, $document->getInvoiceNumber());
        $this->assertEquals(
            $packageInfo[$packageSequenceNumber]['params']['content_type'],
            $document->getExportType()
        );
        $this->assertEquals(
            $packageInfo[$packageSequenceNumber]['params']['content_type_other'],
            $document->getExportTypeDescription()
        );
        $this->assertEquals($customsInfo['terms_of_trade'], $document->getTermsOfTrade());
        $this->assertEquals($customsInfo['additional_fee'], $document->getAdditionalFee());
        $this->assertEquals($customsInfo['permit_number'], $document->getPermitNumber());
        $this->assertEquals($customsInfo['attestation_number'], $document->getAttestationNumber());
        $this->assertEquals($customsInfo['export_notification'], $document->isElectronicExportNotification());


        $positions = $document->getPositions();
        $this->assertInstanceOf(Export\PositionCollection::class, $positions);
        $this->assertCount(2, $positions);

        $position = $positions->getItem($itemSequenceNumberOne);
        $this->assertEquals($itemSequenceNumberOne, $position->getSequenceNumber());
        $this->assertEquals(
            $package['items'][$itemSequenceNumberOne]['customs']['description'],
            $position->getDescription()
        );
        $this->assertEquals(
            $package['items'][$itemSequenceNumberOne]['customs']['country_of_origin'],
            $position->getCountryCodeOrigin()
        );
        $this->assertEquals(
            $package['items'][$itemSequenceNumberOne]['customs']['tariff_number'],
            $position->getTariffNumber()
        );
        $this->assertEquals($package['items'][$itemSequenceNumberOne]['qty'], $position->getAmount());
        $this->assertEquals($package['items'][$itemSequenceNumberOne]['weight'], $position->getNetWeightInKG());
        $this->assertEquals($package['items'][$itemSequenceNumberOne]['customs_value'], $position->getValue());

        $position = $positions->getItem($itemSequenceNumberTwo);
        $this->assertEquals($itemSequenceNumberTwo, $position->getSequenceNumber());
        $this->assertEquals(
            $package['items'][$itemSequenceNumberTwo]['customs']['description'],
            $position->getDescription()
        );
        $this->assertEquals(
            $package['items'][$itemSequenceNumberTwo]['customs']['country_of_origin'],
            $position->getCountryCodeOrigin()
        );
        $this->assertEquals(
            $package['items'][$itemSequenceNumberTwo]['customs']['tariff_number'],
            $position->getTariffNumber()
        );
        $this->assertEquals($package['items'][$itemSequenceNumberTwo]['qty'], $position->getAmount());
        $this->assertEquals($package['items'][$itemSequenceNumberTwo]['weight'], $position->getNetWeightInKG());
        $this->assertEquals($package['items'][$itemSequenceNumberTwo]['customs_value'], $position->getValue());
    }
}
