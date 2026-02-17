<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\ParcelDe\Service\Type;

abstract class Boolean extends Generic
{
    protected $frontendInputType = 'boolean';

    /**
     * Render the checkbox input element for this service.
     *
     * @return string
     */
    public function renderCheckboxInput()
    {
        $format = <<<'HTML'
<input type="checkbox" id="shipment_service_%s" name="shipment_service[%s]" value="%s" class="checkbox" %s />
HTML;

        $selected = $this->isSelected() ? 'checked="checked"' : '';
        return sprintf($format, $this->getCode(), $this->getCode(), $this->getCode(), $selected);
    }

    /**
     * @return string
     */
    public function getSelectorHtml()
    {
        return $this->renderCheckboxInput();
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
     * @return string
     */
    public function getValueHtml()
    {
        return '';
    }
}
