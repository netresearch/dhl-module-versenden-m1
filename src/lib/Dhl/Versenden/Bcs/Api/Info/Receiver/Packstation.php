<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Info\Receiver;

use Dhl\Versenden\Bcs\Api\Info;

class Packstation extends PostalFacility
{
    /** @var string */
    public $packstationNumber;
    /** @var string */
    public $postNumber;
}
