<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Shipment\Service\Type;

abstract class Boolean extends Generic
{
    protected $frontendInputType = 'boolean';

    /**
     * @return string
     */
    public function getSelectorHtml()
    {
        $format = <<<'HTML'
<input type="checkbox" id="shipment_service_%s" name="shipment_service[%s]" value="%s" class="checkbox" %s />
HTML;

        $selected = (bool)$this->isSelected() ? 'checked="checked"' : '';
        return sprintf($format, $this->getCode(), $this->getCode(), $this->getCode(), $selected);
    }

    /**
     * @return string
     */
    public function getLabelHtml()
    {
        $format = <<<'HTML'
<label for="shipment_service_%s">%s</label>
HTML;

        return sprintf($format, $this->getCode(), $this->getName());
    }

    /**
     * No service details for boolean form elements.
     *
     * @return string
     */
    public function getValueHtml()
    {
        return '';
    }
}
