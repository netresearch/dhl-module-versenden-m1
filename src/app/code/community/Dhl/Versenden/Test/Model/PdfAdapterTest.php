<?php

/**
 * See LICENSE.md for license details.
 */

use Dhl\Versenden\ParcelDe\Service\Collection as ServiceCollection;
use Dhl\Versenden\ParcelDe\Service;

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

        $inputPages = [
            $inLabelDefault->render(),
            $inLabelExport->render(),
            $inLabelReturn->render(),
            '', // skipped
            false,  // skipped
        ];

        $pdfAdapter = new Dhl\Versenden\ParcelDe\Pdf\Adapter\Zend();
        $mergedPdfString = $pdfAdapter->merge($inputPages);


        $mergedPdf = Zend_Pdf::parse($mergedPdfString);
        static::assertCount($inputPagesCount, $mergedPdf->pages);
    }
}
