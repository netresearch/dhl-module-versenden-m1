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
use Dhl\Versenden\Config\Exception as ConfigException;
/**
 * Config
 *
 * @category Dhl
 * @package  Dhl\Versenden
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Config
{
    /** @var string[] */
    protected $carrierConfig;

    /**
     * Dhl_Versenden_Config constructor.
     * @param string[] $carrierConfig
     */
    public function __construct($carrierConfig = array())
    {
        $this->carrierConfig = $carrierConfig;
    }

    /**
     * @param string $idx
     * @param mixed $default
     * @return mixed|string
     */
    public function getValue($idx, $default = '')
    {
        if (!isset($this->carrierConfig[$idx])) {
            return $default;
        }

        return $this->carrierConfig[$idx];
    }

    /**
     * Validate a config setting's string length.
     *
     * @param string $name The label/key
     * @param string $value The value to be validated
     * @param int $minLength The minimum allowed string length
     * @param int $maxLength The maximum allowed string length
     * @return bool
     * @throws ConfigException
     */
    public function validateLength($name, $value, $minLength, $maxLength)
    {
        if ( ($minLength > 0) && !strlen($value) ) {
            throw new ConfigException("$name is a required value.");
        }

        if (strlen($value) < $minLength) {
            throw new ConfigException("Please enter at least $minLength characters for $name.");
        }

        if (strlen($value) > $maxLength) {
            throw new ConfigException("Please enter no more than $maxLength characters for $name.");
        }

        return true;
    }
}
