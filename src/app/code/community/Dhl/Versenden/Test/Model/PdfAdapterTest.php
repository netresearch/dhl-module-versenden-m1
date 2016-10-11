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
use \Dhl\Versenden\Shipment\Service\Collection as ServiceCollection;
use \Dhl\Versenden\Shipment\Service;
/**
 * Dhl_Versenden_Test_Model_PdfAdapterTest
 *
 * @category Dhl
 * @package  Dhl_Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Dhl_Versenden_Test_Model_PdfAdapterTest extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     */
    public function merge()
    {
        $inLabelDefault = new Zend_Pdf();
        $inLabelDefault->pages[] = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);

        $inLabelExport = new Zend_Pdf();
        $inLabelExport->pages[] = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);
        $inLabelExport->pages[] = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);

        $inLabelReturn = new Zend_Pdf();
        $inLabelReturn->pages[] = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);

        $inputPagesCount = count($inLabelDefault->pages) + count($inLabelExport->pages) + count($inLabelReturn->pages);

        $inputPages = array(
            $inLabelDefault->render(),
            $inLabelExport->render(),
            $inLabelReturn->render(),
            '', // skipped
            false  // skipped
        );

        $pdfAdapter = new Dhl\Versenden\Pdf\Adapter\Zend();
        $mergedPdfString = $pdfAdapter->merge($inputPages);


        $mergedPdf = Zend_Pdf::parse($mergedPdfString);
        $this->assertCount($inputPagesCount, $mergedPdf->pages);
    }
}
