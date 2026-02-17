<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\ParcelDe\Service;

class Endorsement extends Type\Select
{
    public const CODE = 'endorsement';
    public const LABEL = 'Endorsement';

    public const RETURN = 'RETURN';
    public const ABANDON = 'ABANDON';
}
