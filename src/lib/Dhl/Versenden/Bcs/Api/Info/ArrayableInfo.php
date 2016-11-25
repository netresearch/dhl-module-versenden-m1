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
 * @package   Dhl\Versenden\Bcs\Api\Info
 * @author    Christoph Aßmann <christoph.assmann@netresearch.de>
 * @copyright 2016 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
namespace Dhl\Versenden\Bcs\Api\Info;

/**
 * ArrayableInfo
 *
 * @category Dhl
 * @package  Dhl\Versenden\Bcs\Api\Info
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
abstract class ArrayableInfo extends AbstractInfo implements ArrayableInterface
{
    /**
     * @param string $arrayKey
     * @return string
     */
    protected static function camelize($arrayKey)
    {
        // separeate
        $arrayKey = str_replace('_', ' ', $arrayKey);
        // camelize separated words
        $arrayKey = ucwords($arrayKey);
        // remove whitespace
        $arrayKey = str_replace(' ', '', $arrayKey);
        // convert first character to lower case
        $arrayKey = lcfirst($arrayKey);

        return $arrayKey;
    }

    /**
     * @param string $propertyName
     * @return string
     */
    protected static function underscore($propertyName)
    {
        // separate
        $propertyName = preg_replace('/(.)([A-Z])/', "$1_$2", $propertyName);
        // convert to lower case
        $propertyName = strtolower($propertyName);

        return $propertyName;
    }

    /**
     * @param bool $underscoreKeys
     * @return mixed[]
     */
    public function toArray($underscoreKeys = true)
    {
        $getter = function ($value) use ($underscoreKeys) {
            if ($value instanceof ArrayableInterface) {
                return $value->toArray($underscoreKeys);
            } else {
                return $value;
            }
        };

        $keysMapper = function ($key) {
            return static::underscore($key);
        };

        $properties = get_object_vars($this);

        $result = array_map($getter, $properties);
        if ($underscoreKeys) {
            $keys = array_map($keysMapper, array_keys($properties));
            $result = array_combine($keys, array_values($result));
        }

        return $result;
    }

    /**
     * @param mixed[] $values
     * @param bool $camelizeKeys
     */
    public function fromArray(array $values, $camelizeKeys = true)
    {
        $setter = function ($value, $key) use ($camelizeKeys) {
            $key = $camelizeKeys ? static::camelize($key) : $key;

            if (property_exists($this, $key)) {
                if ($this->{$key} instanceof ArrayableInterface && is_array($value)) {
                    $method    = 'fromArray';
                    $params    = [$value, $camelizeKeys];
                    call_user_func_array([$this->{$key}, $method], $params);
                } elseif ($this->{$key} instanceof UnserializableInterface && is_object($value)) {
                    $className = get_class($this->{$key});
                    $method    = 'fromObject';
                    $params    = [$value];
                    $this->{$key} = call_user_func_array([$className, $method], $params);
                } else {
                    $this->{$key} = $value;
                }
            }
        };

        array_walk($values, $setter);
    }

    /**
     * @param \stdClass $object
     * @return Receiver|null
     */
    public static function fromObject(\stdClass $object)
    {
        $instance = new static();
        $properties = get_object_vars($object);
        $instance->fromArray($properties);

        return $instance;
    }
}
