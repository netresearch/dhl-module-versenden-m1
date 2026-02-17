<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\ParcelDe\Service;

class ParcelAnnouncement extends Type\Boolean
{
    public const CODE = 'parcelAnnouncement';
    public const LABEL = 'Parcel announcement';
    public const DISPLAY_MODE_OPTIONAL = '2';

    /**
     * ParcelAnnouncement constructor.
     *
     * @param string $name
     * @param int $isEnabled
     * @param bool $isSelected
     */
    public function __construct($name, $isEnabled, $isSelected)
    {
        if ($isEnabled === self::DISPLAY_MODE_OPTIONAL) {
            // customer can decide
            $this->customerService = true;
        }
        parent::__construct($name, (bool) $isEnabled, $isSelected);
    }
}
