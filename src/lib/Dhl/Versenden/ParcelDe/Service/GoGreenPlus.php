<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\ParcelDe\Service;

class GoGreenPlus extends Type\Boolean
{
    /**
     * Service code kept as 'goGreen' for backward compatibility with serialized
     * shipment data created before the class was renamed from GoGreen to GoGreenPlus.
     */
    public const CODE = 'goGreen';
    public const LABEL = 'GoGreen Plus';

    protected $customerService = true;
}
