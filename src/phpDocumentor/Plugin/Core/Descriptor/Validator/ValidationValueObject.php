<?php

namespace phpDocumentor\Plugin\Core\Descriptor\Validator;

/**
 *
 * @property \phpDocumentor\Descriptor\ArgumentDescriptor $argument
 * @property \phpDocumentor\Descriptor\Tag\ParamDescriptor $parameter
 * @property \phpDocumentor\Descriptor\Collection $parameters
 * @property string $fqsen
 * @property string $name
 * @property string $index
 * @property string $key
 */
class ValidationValueObject extends \ArrayObject
{
    protected $data = array(
        'arguments' => '',
        'argument' => '',
        'parameter' => '',
        'parameters' => '',
        'fqsen' => '',
        'name' => '',
        'index' => 0,
        'key' => 0,
    );

    public function __get($name)
    {
        if (!isset($this->data[$name])) {
            throw new \BadMethodCallException("Property {$name} not supported.");
        }

        return $this->data[$name];
    }

    public function __set($name, $value)
    {
        if (!isset($this->data[$name])) {
            throw new \BadMethodCallException("Property {$name} not supported.");
        }

        if (!empty($value)) {
            $this->data[$name] = $value;
        }
    }

    public function __isset($name)
    {
        return array_key_exists($name, $this->data);
    }
}
