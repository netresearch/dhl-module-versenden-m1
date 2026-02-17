<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\ParcelDe\Config\Data;

/**
 * Label format type constants
 *
 * Defines the available label response formats from the DHL API.
 */
class LabelType
{
    /**
     * Base64-encoded label format
     */
    public const LABEL_TYPE_B64 = 'B64';

    /**
     * URL-based label format
     */
    public const LABEL_TYPE_URL = 'URL';
}
