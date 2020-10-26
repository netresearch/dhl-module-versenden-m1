<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Info\Receiver;

use Dhl\Versenden\Bcs\Api\Info;

class ParcelShop extends PostalFacility
{
    /** @var string */
    public $parcelShopNumber;
    /** @var string */
    public $streetName;
    /** @var string */
    public $streetNumber;
}
