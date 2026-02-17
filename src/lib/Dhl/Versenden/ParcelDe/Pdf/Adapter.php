<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\ParcelDe\Pdf;

interface Adapter
{
    /**
     * @param string[] $pages
     * @return string
     */
    public function merge($pages);
}
