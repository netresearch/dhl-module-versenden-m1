<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\ParcelDe\Info\Receiver;

use Dhl\Versenden\ParcelDe\Info;

class ParcelShop extends PostalFacility
{
    /** @var string */
    public $parcelShopNumber;
    /** @var string */
    public $streetName;
    /** @var string */
    public $streetNumber;
}
