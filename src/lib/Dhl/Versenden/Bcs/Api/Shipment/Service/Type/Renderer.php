<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\Bcs\Api\Shipment\Service\Type;

class Renderer
{
    /** @var Generic */
    protected $service;
    /** @var bool */
    protected $readOnly;
    /** @var string */
    protected $selectedYes = 'Yes';
    /** @var string */
    protected $selectedNo  = 'No';


    /**
     * Renderer constructor.
     * @param Generic $service
     * @param bool $readOnly
     */
    public function __construct(Generic $service, $readOnly = false)
    {
        $this->service = $service;
        $this->readOnly = $readOnly;
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
            return '';
        }

        return $this->service->getSelectorHtml();
    }

    /**
     * @return string
     */
    public function getLabelHtml()
    {
        if ($this->readOnly) {
            return $this->service->getName();
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
