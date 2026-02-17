<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\ParcelDe\Pdf\Adapter;

use Dhl\Versenden\ParcelDe\Pdf\Adapter;

class Zend implements Adapter
{
    /**
     * @param string[] $pages
     * @return string
     */
    public function merge($pages)
    {
        $pages = array_filter($pages);
        if (count($pages) === 1) {
            return current($pages);
        }

        $pdfOut = new \Zend_Pdf();

        foreach ($pages as $page) {
            $pdfIn = \Zend_Pdf::parse($page);
            foreach ($pdfIn->pages as $pageIn) {
                $pdfOut->pages[] = clone $pageIn;
            }
        }

        return $pdfOut->render();
    }
}
