<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\ParcelDe\Service\Type;

abstract class Hidden extends Generic
{
    protected $frontendInputType = 'hidden';

    /**
     * @return string
     */
    public function getSelectorHtml()
    {
        $format = '<input type="hidden" name="shipment_service[%s]" value="%s">';
        return sprintf($format, $this->getCode(), (int) $this->isSelected());
    }

    /**
     * No labels for hidden form elements.
     *
     * @return string
     */
    public function getLabelHtml()
    {
        return '';
    }

    /**
     * No service details for hidden form elements.
     *
     * @return string
     */
    public function getValueHtml()
    {
        return '';
    }
}
