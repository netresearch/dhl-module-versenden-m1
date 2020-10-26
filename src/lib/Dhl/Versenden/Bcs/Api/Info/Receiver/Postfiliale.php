<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Info\Receiver;

use Dhl\Versenden\Bcs\Api\Info;

class Postfiliale extends PostalFacility
{
    /** @var string */
    public $postfilialNumber;
    /** @var string */
    public $postNumber;
}
