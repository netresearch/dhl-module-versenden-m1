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
 * @package   Dhl\Versenden\Service
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
namespace Dhl\Versenden\Service;
use Dhl\Versenden\Service;
/**
 * Renderer
 *
 * @category Dhl
 * @package  Dhl\Versenden\Service
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Renderer
{
    /** @var Service */
    protected $service;

    /**
     * Renderer constructor.
     * @param Service $service
     */
    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    /**
     * Obtain the markup for selecting the service (checkbox)
     *
     * @return string
     */
    public function getSelectorHtml()
    {
        $checkboxFormat = <<<'HTML'
<input type="checkbox" id="shipment_service_%s" name="shipment_service[%s]" value="%s" class="checkbox" />
HTML;
        $hiddenFormat = <<<'HTML'
<input type="hidden" name="shipment_service[%s]" value="%s">
HTML;

        $serviceCode = $this->service->getCode();
        switch ($this->service->frontendInputType) {
            case Service::INPUT_TYPE_HIDDEN:
                return sprintf($hiddenFormat, $serviceCode, $this->service->value);
            default:
                return sprintf($checkboxFormat, $serviceCode, $serviceCode, $serviceCode);
        }
    }

    /**
     * Obtain the markup for the service selector label.
     *
     * @param string $serviceName Translated service name
     * @return string
     */
    public function getLabelHtml($serviceName)
    {
        $labelFormat = <<<'HTML'
<label for="shipment_service_%s">%s</label>
HTML;
        $serviceCode = $this->service->getCode();
        switch ($this->service->frontendInputType) {
            case Service::INPUT_TYPE_HIDDEN:
                return '';
            default:
                return sprintf($labelFormat, $serviceCode, $serviceName);
        }
    }

    /**
     * Obtain the markup for service settings (input field or dropdown)
     *
     * @return string
     */
    public function getSettingsHtml()
    {
        $serviceCode = $this->service->getCode();
        switch ($this->service->frontendInputType) {
            case Service::INPUT_TYPE_TEXT:
                $format = <<<'HTML'
<input type="text" name="service_setting[%s]" data-select-id="shipment_service_%s" class="input-text input-with-checkbox" maxlength="100" placeholder="%s" />
HTML;

                return sprintf($format, $serviceCode, $serviceCode, $this->service->getPlaceholder());
                break;
            case Service::INPUT_TYPE_SELECT:
                $format = '<select name="service_setting[%s]">%s</select>';
                $options = $this->service->getOptions();
                $values = array_keys($options);

                $optionsHtml = array_reduce($values, function ($carry, $value) use ($options) {
                    $carry .= sprintf('<option value="%s">%s</option>', $value, $options[$value]);
                    return $carry;
                });

                return sprintf($format, $serviceCode, $optionsHtml);
            default:
                return '';
        }
    }
}
