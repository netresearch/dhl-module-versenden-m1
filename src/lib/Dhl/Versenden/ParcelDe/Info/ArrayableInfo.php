<?php

/**
 * See LICENSE.md for license details.
 */

namespace Dhl\Versenden\ParcelDe\Info;

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
        $propertyName = preg_replace('/(.)([A-Z])/', '$1_$2', $propertyName);
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
            }
            return $value;
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
     * @return static|null
     * @phpstan-return static
     */
    public static function fromObject(\stdClass $object)
    {
        /** @phpstan-ignore-next-line new.static,new.staticInAbstractClassStaticMethod */
        $instance = new static();
        $properties = get_object_vars($object);
        $instance->fromArray($properties);

        return $instance;
    }
}
