<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\ParcelDe\Info\Receiver;

use Dhl\Versenden\ParcelDe\Info;

class Postfiliale extends PostalFacility
{
    /** @var string */
    public $postfilialNumber;
    /** @var string */
    public $postNumber;
}
