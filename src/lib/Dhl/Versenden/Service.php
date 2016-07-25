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
 * @package   Dhl\Versenden
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
namespace Dhl\Versenden;
use Dhl\Versenden\Service\Renderer;

/**
 * Service
 *
 * @category Dhl
 * @package  Dhl\Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
abstract class Service
{
    const INPUT_TYPE_BOOLEAN = 'boolean';
    const INPUT_TYPE_DATE    = 'date';
    const INPUT_TYPE_HIDDEN  = 'hidden';
    const INPUT_TYPE_SELECT  = 'select';
    const INPUT_TYPE_TEXT    = 'text';

    /** @var string */
    public $name = 'Service';
    /** @var bool */
    public $isCustomerService = false;
    /** @var string */
    public $frontendInputType = self::INPUT_TYPE_TEXT;
    /** @var string */
    public $value = '';

    /**
     * Service constructor.
     * @param string $value Service setting preselection.
     */
    public function __construct($value = '')
    {
        if (strpos($this->frontendInputType, 'bool') === 0) {
            $this->value = (bool)$value;
        } else {
            $this->value = $value;
        }
    }

    /**
     * Infer service code from class name.
     *
     * @return string
     */
    public function getCode()
    {
        $className = get_class($this);
        if ($pos = strrpos($className, '\\')) {
            $className = substr($className, $pos + 1);
        }
        return lcfirst($className);
    }

    /**
     * Obtain the frontend input renderer for this service.
     *
     * @return Renderer
     */
    public function getRenderer()
    {
        return new Renderer($this);
    }
}
