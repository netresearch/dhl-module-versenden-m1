<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\ParcelDe\Service;

class ClosestDropPoint extends Type\Boolean
{
    public const CODE = 'closestDropPoint';
    public const LABEL = 'Closest Drop Point';

    protected $customerService = true;
}
