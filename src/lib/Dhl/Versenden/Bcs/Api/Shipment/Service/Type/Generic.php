<?php
/**
* Dhl Versenden
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this extension to
* newer versions in the future.
*
* PHP version 5
*
* @category  Dhl
* @package   Dhl\Versenden\Bcs\Api\Service
* @author    Christoph Aßmann <christoph.assmann@netresearch.de>
* @copyright 2016 Netresearch GmbH & Co. KG
* @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
* @link      http://www.netresearch.de/
*/
namespace Dhl\Versenden\Bcs\Api\Shipment\Service\Type;

/**
 * Generic
 *
 * @category Dhl
 * @package  Dhl\Versenden\Bcs\Api\Service
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
abstract class Generic
{
    const CODE = 'Service';

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
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @return boolean
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
     * @return boolean
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
        $this->selected = (bool)$value;
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
