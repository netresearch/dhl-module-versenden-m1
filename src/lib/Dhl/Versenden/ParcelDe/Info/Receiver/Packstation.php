<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\ParcelDe\Info\Receiver;

use Dhl\Versenden\ParcelDe\Info;

class Packstation extends PostalFacility
{
    /** @var string */
    public $packstationNumber;
    /** @var string */
    public $postNumber;
}
