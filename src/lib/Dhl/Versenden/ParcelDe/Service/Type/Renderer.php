<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\ParcelDe\Service\Type;

class Renderer
{
    /** @var Generic */
    protected $service;
    /** @var bool */
    protected $readOnly;
    /** @var bool */
    protected $adminMode;
    /** @var string */
    protected $selectedYes = 'Yes';
    /** @var string */
    protected $selectedNo  = 'No';


    /**
     * Renderer constructor.
     * @param Generic $service
     * @param bool $readOnly
     * @param bool $adminMode
     */
    public function __construct(Generic $service, $readOnly = false, $adminMode = false)
    {
        $this->service = $service;
        $this->readOnly = $readOnly;
        $this->adminMode = $adminMode;
    }

    /**
     * @return bool
     */
    public function isReadOnly()
    {
        return $this->readOnly;
    }

    /**
     * @param string $selectedYes
     */
    public function setSelectedYes($selectedYes)
    {
        $this->selectedYes = $selectedYes;
    }

    /**
     * @param string $selectedNo
     */
    public function setSelectedNo($selectedNo)
    {
        $this->selectedNo = $selectedNo;
    }

    /**
     * @return string
     */
    public function getSelectorHtml()
    {
        if ($this->readOnly) {
            $checked = $this->service->isSelected() ? ' checked="checked"' : '';
            return sprintf(
                '<input type="checkbox" id="shipment_service_%s" name="shipment_service[%s]" value="%s" class="checkbox" disabled="disabled" data-locked="1"%s />',
                $this->service->getCode(),
                $this->service->getCode(),
                $this->service->getCode(),
                $checked
            );
        }

        return $this->service->getSelectorHtml();
    }

    /**
     * Strip ": description" suffix from service name for compact admin labels.
     *
     * @return string
     */
    protected function getAdminShortName()
    {
        $name = $this->service->getName();
        $colonPos = strpos($name, ': ');
        if ($colonPos !== false) {
            return substr($name, 0, $colonPos);
        }
        return $name;
    }

    /**
     * @return string
     */
    public function getLabelHtml()
    {
        if ($this->readOnly) {
            return sprintf('<label for="shipment_service_%s">%s</label>', $this->service->getCode(), $this->service->getName());
        }

        if ($this->adminMode && $this->service instanceof Text) {
            $format = <<<'HTML'
<label for="shipment_service_%sDetails">%s</label>
HTML;
            return sprintf($format, $this->service->getCode(), $this->getAdminShortName());
        }

        return $this->service->getLabelHtml();
    }

    /**
     * @return string
     */
    public function getValueHtml()
    {
        if ($this->readOnly) {
            $value = $this->selectedNo;
            if ($this->service instanceof Text && $this->service->getValue()) {
                $value = $this->service->getValue();
            } elseif ($this->service->isSelected()) {
                $value = $this->selectedYes;
            }
            return $value;
        }

        return $this->service->getValueHtml();
    }
}
