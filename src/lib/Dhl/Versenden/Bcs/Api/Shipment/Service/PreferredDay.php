<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Shipment\Service;

class PreferredDay extends Type\Radio
{
    const CODE = 'preferredDay';

    /**
     * DeliveryTimeFrame constructor.
     *
     * @param string   $name
     * @param bool     $isEnabled
     * @param bool     $isSelected
     * @param string[] $options
     */
    public function __construct($name, $isEnabled, $isSelected, $options)
    {
        $this->customerService = true;

        parent::__construct($name, $isEnabled, $isSelected, $options);
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
            function($carry, $value) use ($options) {
                $checked      = ($value == $this->getValue()) ? 'checked="checked"' : '';
                $disabled     =
                    (isset($options[$value]['disabled']) && $options[$value]['disabled']) ? 'disabled=disabled' : '';
                $optionValues = explode('-', $options[$value]['value']);
                $carry .= sprintf(
                    '<div>' .
                    '<input type="radio" name="service_setting[%s]" id="shipment_service_%s" %s %s value="%s">' .
                    '<label for="shipment_service_%s" title="%s"><span>%s</span><span>%s</span></label>' .
                    '</div>',
                    $this->getCode(),
                    $this->getCode() . '_' . $value,
                    $checked,
                    $disabled,
                    $value,
                    $this->getCode() . '_' . $value,
                    $value,
                    $optionValues[0],
                    $optionValues[1]
                );

                return $carry;
            }
        );

        return $optionsHtml;
    }
}
