<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Pdf;

interface Adapter
{
    /**
     * @param string[] $pages
     * @return string
     */
    public function merge($pages);
}
