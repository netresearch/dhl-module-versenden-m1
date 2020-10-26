<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Shipment\Service;

class PreferredTime extends Type\Radio
{
    const CODE = 'preferredTime';

    /**
     * DeliveryTimeFrame constructor.
     *
     * @param string $name
     * @param bool $isEnabled
     * @param bool $isSelected
     * @param string[] $options
     */
    public function __construct($name, $isEnabled, $isSelected, $options)
    {
        $this->customerService = true;

        parent::__construct($name, $isEnabled, $isSelected, $options);
    }
}
