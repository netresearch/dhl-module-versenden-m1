<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\ParcelDe\Service\Type;

abstract class Radio extends Text
{
    protected $frontendInputType = 'radio';

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
     * @return string[]
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
        $options = $this->getOptions();
        $values  = array_keys($options);

        $optionsHtml = array_reduce(
            $values,
            function ($carry, $value) use ($options) {
                $checked = ($value == $this->getValue()) ? 'checked="checked"' : '';
                $carry .= sprintf(
                    '<div>' .
                    '<input type="radio" name="service_setting[%s]" id="shipment_service_%s" %s value="%s">' .
                    '<label for="shipment_service_%s">%s</label>' .
                    '</div>',
                    $this->getCode(),
                    $this->getCode() . '_' . $value,
                    $checked,
                    $value,
                    $this->getCode() . '_' . $value,
                    (string) $options[$value],
                );

                return $carry;
            },
        );

        return $optionsHtml;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }
}
