<?php

declare(strict_types=1);

namespace Blackprism\Serializer\Json;

use Blackprism\Serializer\Configuration;
use Blackprism\Serializer\Configuration\ObjectInterface;
use Blackprism\Serializer\Configuration\Type;
use Blackprism\Serializer\Exception\InvalidObject;
use Blackprism\Serializer\SerializerInterface;
use Blackprism\Serializer\Value\ClassName;

/**
 * Serialize
 *
 * @property Configuration $configuration
 */
class Serialize implements SerializerInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Serialize object
     *
     * @param Object $object
     *
     * @return string
     * @throws InvalidObject
     */
    public function serialize($object): string
    {
        if (is_object($object) === false) {
            throw new InvalidObject('Argument is not an object (passed argument type is ' . gettype($object) . ')');
        }

        $jsonEncoded = json_encode($this->setArray($object));

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidObject(json_last_error_msg());
        }

        return $jsonEncoded;
    }

    /**
     * Create array from object
     *
     * @param Object $object
     *
     * @return mixed[string]
     */
    private function setArray($object): array
    {
        $data = [];

        $configurationObject = $this->configuration->getConfigurationObjectForClass(new ClassName(get_class($object)));

        foreach ($configurationObject->getAttributes() as $attribute) {
            $type = $configurationObject->getTypeForAttribute($attribute);
            $data = $this->processSerializeForType($type, $object, $data, $attribute);
        }

        return $data;
    }

    /**
     * @param Type $type
     * @param Object $object
     * @param mixed[string] $data
     * @param ObjectInterface $attribute
     *
     * @return mixed[string]
     */
    private function processSerializeForType(Type $type, $object, array $data, $attribute): array
    {
        if ($type instanceof Type\Method) {
            $data = $this->processSerializeTypeMethod($type, $object, $data, $attribute);
        } elseif ($type instanceof Type\Object) {
            $data = $this->processSerializeTypeObject($type, $object, $data, $attribute);
        } elseif ($type instanceof Type\Handler) {
            $data = $this->processSerializeTypeHandler($type, $object, $data, $attribute);
        }

        return $data;
    }

    /**
     * @param Type\Method $method
     * @param Object $object
     * @param mixed[string] $data
     * @param string $attribute
     *
     * @return mixed[string]
     */
    private function processSerializeTypeMethod(Type\Method $method, $object, array $data, string $attribute): array
    {
        $value = $object->{$method->getter()}();

        if ($this->checkNullForAttribute($value, $attribute) === false) {
            $data[$attribute] = $value;
        }

        return $data;
    }

    /**
     * @param Type\Object $objectType
     * @param Object $object
     * @param mixed[string] $data
     * @param string $attribute
     *
     * @return mixed[string]
     */
    private function processSerializeTypeObject(Type\Object $objectType, $object, array $data, string $attribute): array
    {
        if ($objectType->isCollection() === true) {
            return $this->processSerializeTypeObjectCollection($objectType, $object, $data, $attribute);
        }

        return $this->setArrayAndCheckNull($data, $object->{$objectType->getter()}(), $attribute);
    }

    /**
     * @param Type\Object $objectType
     * @param Object $object
     * @param mixed[string] $data
     * @param string $attribute
     *
     * @return mixed[string]
     */
    private function processSerializeTypeObjectCollection(
        Type\Object $objectType,
        $object,
        array $data,
        string $attribute
    ): array {
        foreach ($object->{$objectType->getter()}() as $key => $subObject) {
            $data = $this->setArrayAndCheckNullWithKey($data, $subObject, $key, $attribute);
        }

        return $data;
    }

    /**
     * @param mixed[string] $data
     * @param Object $object
     * @param string $attribute
     *
     * @return mixed[string]
     */
    private function setArrayAndCheckNull(array $data, $object, $attribute): array
    {
        $value = $this->setArray($object);

        if ($this->checkNullForAttribute($value, $attribute) === false) {
            $data[$attribute]= $value;
        }

        return $data;
    }

    /**
     * @param mixed[string] $data
     * @param Object $object
     * @param mixed $key
     * @param string $attribute
     *
     * @return mixed[string]
     */
    private function setArrayAndCheckNullWithKey(array $data, $object, $key, $attribute): array
    {
        $value = $this->setArray($object);

        if ($this->checkNullForAttribute($value, $key) === false) {
            $data[$attribute][$key] = $value;
        }

        return $data;
    }

    /**
     * @param Type\Handler $handler
     * @param Object $object
     * @param mixed[string] $data
     * @param string $attribute
     *
     * @return mixed[string]
     */
    private function processSerializeTypeHandler(Type\Handler $handler, $object, array $data, string $attribute): array
    {
        $value = $handler->serializer()->serialize($object);

        if ($this->checkNullForAttribute($value, $attribute) === false) {
            $data[$attribute] = $value;
        }

        return $data;
    }

    /**
     * Set attribute with value when value is not considered as a null value
     *
     * @param mixed $value
     * @param string $attribute
     *
     * @return bool
     */
    protected function checkNullForAttribute($value, $attribute): bool
    {
        // We don't use $attribute here, we want filter all value with the same behavior

        if ($value === null
            || is_array($value) === true && $value === []
            || $value instanceof \Countable && count($value) === 0) {
            return true;
        }

        return false;
    }
}
