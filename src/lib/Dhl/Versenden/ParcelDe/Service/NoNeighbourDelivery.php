<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\ParcelDe\Service;

class NoNeighbourDelivery extends Type\Boolean
{
    public const CODE = 'noNeighbourDelivery';
    public const LABEL = 'No Neighbour Delivery';

    protected $customerService = true;
}
