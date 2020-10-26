<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Info\Receiver;

use Dhl\Versenden\Bcs\Api\Info;

abstract class PostalFacility extends Info\ArrayableInfo
{
    const TYPE_PACKSTATION = 'packStation';
    const TYPE_PAKETSHOP   = 'parcelShop';
    const TYPE_POSTFILIALE = 'postOffice';

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
