<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Shipment\Service\Type;

abstract class Text extends Generic
{
    protected $frontendInputType = 'text';

    /**
     * Additional service detail placeholder
     * @var string
     */
    protected $placeholder;

    /**
     * Additional service detail maxlength
     * @var int
     */
    protected $maxLength;

    /**
     * Additional service detail selection
     * @var string
     */
    protected $value;

    /**
     * Text constructor.
     * @param string $name
     * @param bool $isEnabled
     * @param bool $isSelected
     * @param string $placeholder
     * @param int $maxLength
     */
    public function __construct($name, $isEnabled, $isSelected, $placeholder, $maxLength = 100)
    {
        $this->placeholder = $placeholder;
        $this->maxLength = $maxLength;

        parent::__construct($name, $isEnabled, $isSelected);
    }

    /**
     * @return string
     */
    public function getPlaceholder()
    {
        return $this->placeholder;
    }

    /**
     * @return int
     */
    public function getMaxLength()
    {
        return $this->maxLength;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return htmlentities($this->value);
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
        parent::setValue($value);
    }

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
<label for="shipment_service_%sDetails">%s</label>
HTML;

        return sprintf($format, $this->getCode(), $this->getName());
    }

    /**
     * @return string
     */
    public function getValueHtml()
    {
        $format = <<<'HTML'
<input type="text" name="service_setting[%s]" value="%s" class="input-text input-with-checkbox"
       maxlength="%d" id="shipment_service_%sDetails" data-select-id="shipment_service_%s" placeholder="%s" />
HTML;

        return sprintf(
            $format,
            $this->getCode(),
            htmlspecialchars($this->getValue(), ENT_COMPAT, 'UTF-8', false),
            $this->getMaxLength(),
            $this->getCode(),
            $this->getCode(),
            $this->getPlaceholder()
        );

    }
}
