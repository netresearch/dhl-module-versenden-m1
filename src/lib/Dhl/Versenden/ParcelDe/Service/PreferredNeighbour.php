<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\ParcelDe\Service;

class PreferredNeighbour extends Type\Text
{
    public const CODE = 'preferredNeighbour';
    public const LABEL = 'Preferred neighbor';

    /**
     * PreferredNeighbour constructor.
     *
     * @param string $name
     * @param bool   $isEnabled
     * @param bool   $isSelected
     * @param string $placeholder
     * @param int    $maxLength
     */
    public function __construct($name, $isEnabled, $isSelected, $placeholder, $maxLength = 100)
    {
        $this->customerService = true;

        parent::__construct($name, $isEnabled, $isSelected, $placeholder, $maxLength);
    }

    /**
     * @return string
     */
    public function getValueHtml()
    {
        $format = <<<'HTML'
<input type="text" name="service_setting[%s]" value="%s" class="input-text input-with-checkbox validate-with-location validate-detail validate-special"
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
