<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Shipment\Service\Type;

abstract class Select extends Text
{
    protected $frontendInputType = 'select';

    /**
     * List of possible service values.
     * @see GenericWithDetails::$value
     * Format:
     *   key => localized value
     * @var string[]
     */
    protected $options;

    /**
     * Generic service constructor.
     *
     * @param string $name
     * @param bool $isEnabled
     * @param bool $isSelected
     * @param string[] $options
     */
    public function __construct($name, $isEnabled, $isSelected, $options)
    {
        $this->options = $options;

        parent::__construct($name, $isEnabled, $isSelected, '');
    }

    /**
     * @return \string[]
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return string
     */
    public function getValueHtml()
    {
        $format = '<select name="service_setting[%s]" id="shipment_service_%sDetails">%s</select>';
        $options = $this->getOptions();
        $values = array_keys($options);

        $optionsHtml = array_reduce(
            $values,
            function ($carry, $value) use ($options) {
                $selected = ($value == $this->getValue()) ? 'selected="selected"' : '';
                $carry .= sprintf('<option %s value="%s">%s</option>', $selected, $value, $options[$value]);
                return $carry;
            }
        );

        return sprintf(
            $format,
            $this->getCode(),
            $this->getCode(),
            $optionsHtml
        );
    }
}
