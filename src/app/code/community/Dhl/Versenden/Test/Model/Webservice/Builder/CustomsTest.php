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
use \Dhl\Versenden\Bcs\Api\Webservice\RequestData\ShipmentOrder\Export;
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
    protected $minWeightInKG = 0.01;

    /**
     * @return string[]
     */
    public function getWeightDataProvider()
    {
        return [
            'weight_in_kg' => [
                'weightUnit' => 'KG',
                'totalWeightInUnit' => 2.0000,
                'totalWeightInKg' => 2.0000,
                'itemOneWeightInUnit' => 0.4000,
                'itemOneWeightInKg' => 0.4000,
                'itemTwoWeightInUnit' => 1.2000,
                'itemTwoWeightInKg' => 1.2000,
            ],
            'weight_in_g' => [
                'weightUnit' => 'G',
                'totalWeightInUnit' => 2000.0000,
                'totalWeightInKg' => 2.0000,
                'itemOneWeightInUnit' => 400.0000,
                'itemOneWeightInKg' => 0.4000,
                'itemTwoWeightInUnit' => 1200.0000,
                'itemTwoWeightInKg' => 1.2000,
            ],
        ];
    }

    /**
     * @test
     * @expectedException Mage_Core_Exception
     */
    public function constructorArgUnitOfMeasureMissing()
    {
        $args = array(
            'min_weight' => $this->minWeightInKG,
        );
        Mage::getModel('dhl_versenden/webservice_builder_customs', $args);
    }

    /**
     * @test
     * @expectedException Mage_Core_Exception
     */
    public function constructorArgUnitOfMeasureWrongType()
    {
        $args = array(
            'unit_of_measure' => new stdClass(),
            'min_weight'      => $this->minWeightInKG,
        );
        Mage::getModel('dhl_versenden/webservice_builder_customs', $args);
    }

    /**
     * @test
     * @expectedException Mage_Core_Exception
     */
    public function constructorArgMinWeightMissing()
    {
        $args = array(
            'unit_of_measure' => 'G',
        );
        Mage::getModel('dhl_versenden/webservice_builder_customs', $args);
    }

    /**
     * @test
     * @expectedException Mage_Core_Exception
     */
    public function constructorArgMinWeightWrongType()
    {
        $args = array(
            'unit_of_measure' => 'G',
            'min_weight'      => new stdClass(),
        );
        Mage::getModel('dhl_versenden/webservice_builder_customs', $args);
    }

    /**
     * Assert that customs builder returns an empty export doc collection.
     *
     * @test
     */
    public function noCustomsInfoAvailable()
    {
        $args = array(
            'unit_of_measure' => 'KG',
            'min_weight' => $this->minWeightInKG
        );
        $invoiceNumber = '103000002';

        $customsInfo = array();
        $packageInfo = array();

        // transform prepared data to structured request data
        $builder = Mage::getModel('dhl_versenden/webservice_builder_customs', $args);
        $documents = $builder->getExportDocuments($invoiceNumber, $customsInfo, $packageInfo);

        $this->assertInstanceOf(Export\DocumentCollection::class, $documents);
        $this->assertEmpty($documents);
    }

    /**
     * @test
     * @dataProvider getWeightDataProvider
     *
     * @param string $weightUnit
     * @param float $totalWeight
     * @param float $itemOneWeight
     * @param float $itemTwoWeight
     */
    public function getExportDocuments(
        $weightUnit,
        $totalWeightInUnit,
        $totalWeightInKg,
        $itemOneWeightInUnit,
        $itemOneWeightInKg,
        $itemTwoWeightInUnit,
        $itemTwoWeightInKg
    ) {
        $args = array('unit_of_measure' => $weightUnit, 'min_weight' => $this->minWeightInKG);

        // prepare data
        $invoiceNumber = '103000002';

        $packageSequenceNumber = '303';
        $itemSequenceNumberOne = '808';
        $itemSequenceNumberTwo = '909';

        $packageItemOne = array(
            'qty' => 2,
            'weight' => $itemOneWeightInUnit,
            'customs_value' => 9.95,
            'customs' => array(
                'description' => 'one',
                'country_of_origin' => 'TR',
                'tariff_number' => '101',
            ),
        );
        $packageItemTwo = array(
            'qty' => 1,
            'weight' => $itemTwoWeightInUnit,
            'customs_value' => 19.95,
            'customs' => array(
                'description' => 'two',
                'country_of_origin' => 'DE',
                'tariff_number' => '202',
            ),
        );

        $package = array(
            'params' => array(
                'weight' => $totalWeightInUnit,
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

        // transform prepared data to structured request data
        $builder = Mage::getModel('dhl_versenden/webservice_builder_customs', $args);
        $documents = $builder->getExportDocuments($invoiceNumber, $customsInfo, $packageInfo);

        // assert item was added and type check
        $this->assertInstanceOf(Export\DocumentCollection::class, $documents);
        $this->assertCount(1, $documents);
        foreach ($documents as $document) {
            $this->assertInstanceOf(Export\Document::class, $document);
        }

        // assert transformed data object (export document) contains all prepared data
        $document = $documents->getItem($packageSequenceNumber);
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


        // assert transformed data object (export positions) contains all prepared data
        $positions = $document->getPositions();
        $this->assertInstanceOf(Export\PositionCollection::class, $positions);
        $this->assertCount(2, $positions);
        foreach ($positions as $position) {
            $this->assertInstanceOf(Export\Position::class, $position);
        }

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
        $this->assertEquals($itemOneWeightInKg, $position->getNetWeightInKG());
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
        $this->assertEquals($itemTwoWeightInKg, $position->getNetWeightInKG());
        $this->assertEquals($package['items'][$itemSequenceNumberTwo]['customs_value'], $position->getValue());

        // add another item
        $this->assertNull($positions->getItem('unknownSequenceNumber'));
        $positionItems = $positions->getItems();
        $positionItems[] = new Export\Position('unknownSequenceNumber', 'Foo', 'CN', '', 2, 0.8, 19.9);
        $positions->setItems($positionItems);
        $this->assertCount(3, $positions);
        $this->assertNotNull($positions->getItem('unknownSequenceNumber'));

        $packageSequenceNumberTwo = '808';
        $document = new Export\Document(
            $packageSequenceNumberTwo,
            $document->getInvoiceNumber(),
            $document->getExportType(),
            $document->getExportTypeDescription(),
            $document->getTermsOfTrade(),
            $document->getAdditionalFee(),
            $document->getPlaceOfCommital(),
            $document->getPermitNumber(),
            $document->getAttestationNumber(),
            $document->isElectronicExportNotification(),
            $document->getPositions()
        );
        $documents->setItems(array($document));
        $this->assertNull($documents->getItem($packageSequenceNumber));
        $this->assertNotNull($documents->getItem($packageSequenceNumberTwo));
    }
}
