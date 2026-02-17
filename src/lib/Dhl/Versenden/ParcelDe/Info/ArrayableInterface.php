<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\ParcelDe\Info;

interface ArrayableInterface
{
    /**
     * @param bool $underscoreKeys
     * @return mixed[]
     */
    public function toArray($underscoreKeys = true);

    /**
     * @param mixed[] $values
     * @param bool $camelizeKeys
     */
    public function fromArray(array $values, $camelizeKeys = true);
}
