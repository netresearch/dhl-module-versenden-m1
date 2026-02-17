<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\ParcelDe\Info\Receiver;

use Dhl\Versenden\ParcelDe\Info;

abstract class PostalFacility extends Info\ArrayableInfo
{
    public const TYPE_PACKSTATION = 'packStation';
    public const TYPE_PAKETSHOP   = 'parcelShop';
    public const TYPE_POSTFILIALE = 'postOffice';

    /** @var string */
    public $zip;
    /** @var string */
    public $city;
    /** @var string */
    public $country;
    /** @var string */
    public $countryISOCode;
    /** @var string */
    public $state;
}
