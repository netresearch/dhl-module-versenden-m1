<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\ParcelDe\Service;

class ParcelOutletRouting extends Type\Text
{
    public const CODE = 'parcelOutletRouting';
    public const LABEL = 'Parcel Outlet Routing';

    /**
     * Render email input instead of text input for browser validation.
     *
     * @return string
     */
    public function getValueHtml()
    {
        $format = <<<'HTML'
<input type="email" name="service_setting[%s]" value="%s" class="input-text input-with-checkbox"
       maxlength="%d" id="shipment_service_%sDetails" data-select-id="shipment_service_%s" placeholder="%s" />
HTML;

        return sprintf(
            $format,
            $this->getCode(),
            htmlspecialchars($this->getValue(), ENT_COMPAT, 'UTF-8', false),
            $this->getMaxLength(),
            $this->getCode(),
            $this->getCode(),
            $this->getPlaceholder(),
        );
    }
}
