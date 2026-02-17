<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\ParcelDe\Service\Type;

abstract class Generic
{
    public const CODE = 'Service';

    protected $frontendInputType = 'generic';

    /**
     * Localized service name.
     *
     * @var string
     */
    protected $name;

    /**
     * Indicates whether service is available for selection or not.
     *
     * @var bool
     */
    protected $enabled;

    /**
     * Indicates whether service can be selected by customer.
     * If set to false, only merchant can select service.
     *
     * @var bool
     */
    protected $customerService = false;

    /**
     * Indicates whether service was selected or not.
     * @var bool
     */
    protected $selected;

    /**
     * Generic service constructor.
     *
     * @param string $name
     * @param bool $isEnabled
     * @param bool $isSelected
     */
    public function __construct($name, $isEnabled, $isSelected)
    {
        $this->name = $name;
        $this->enabled = $isEnabled;
        $this->selected = $isSelected;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return static::CODE;
    }

    /**
     * @return string
     */
    public function getFrontendInputType()
    {
        return $this->frontendInputType;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @return bool
     */
    public function isSelected()
    {
        return $this->selected;
    }

    /**
     * @return bool
     */
    public function getValue()
    {
        return $this->isSelected();
    }

    /**
     * @return bool
     */
    public function isCustomerService()
    {
        return $this->customerService;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->selected = (bool) $value;
    }

    /**
     * @return string
     */
    abstract public function getSelectorHtml();

    /**
     * @return string
     */
    abstract public function getLabelHtml();

    /**
     * @return string
     */
    abstract public function getValueHtml();
}
