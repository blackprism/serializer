<?php

declare(strict_types=1);

namespace Blackprism\Serializer;

use Blackprism\Serializer\Configuration\ObjectInterface;
use Blackprism\Serializer\Configuration\Type;
use Blackprism\Serializer\Exception\InvalidJson;
use Blackprism\Serializer\Exception\InvalidObject;
use Blackprism\Serializer\Value\ClassName;

/**
 * Json
 *
 * @property Configuration $configuration
 */
class Json implements SerializerInterface
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
     * Deserialize string with class as the root object
     *
     * @param string $serialized
     * @param ClassName $className
     *
     * @return Object
     * @throws InvalidJson
     */
    public function deserialize(string $serialized, ClassName $className)
    {
        $objectsAsArray = json_decode($serialized, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidJson(json_last_error_msg());
        }

        return $this->setObject($className, $objectsAsArray);
    }

    /**
     * Create class object with data
     *
     * @param ClassName $className
     * @param array $data
     *
     * @return Object
     */
    private function setObject(ClassName $className, array $data)
    {
        $object = $className->buildObject();

        /**
         * @var string $attribute
         * @var mixed $value
         */
        foreach ($data as $attribute => $value) {
            $configurationObject = $this->configuration->getConfigurationObjectForClass($className);
            $type = $configurationObject->getTypeForAttribute($attribute);

            $this->processDeserializeForType($type, $object, $value);
        }

        return $object;
    }

    /**
     * @param Type $type
     * @param Object $object
     * @param mixed $value
     */
    private function processDeserializeForType(Type $type, $object, $value)
    {
        if ($type instanceof Type\Method) {
            $this->processDeserializeTypeMethod($type, $object, $value);
        } elseif ($type instanceof Type\Object) {
            $this->processDeserializeTypeObject($type, $object, $value);
        } elseif ($type instanceof Type\Handler) {
            $this->processDeserializeTypeHandler($type, $object, $value);
        }
    }

    /**
     * @param Type\Method $method
     * @param Object $object
     * @param mixed $value
     *
     * @return Json
     */
    private function processDeserializeTypeMethod(Type\Method $method, $object, $value): self
    {
        $object->{$method->setter()}($value);

        return $this;
    }

    /**
     * @param Type\Object $objectType
     * @param Object $object
     * @param mixed $value
     *
     * @return Json
     */
    private function processDeserializeTypeObject(Type\Object $objectType, $object, $value): self
    {
        if ($objectType->isCollection() === true) {
            $objects = $this->processDeserializeTypeObjectCollection($objectType, $value);
            $object->{$objectType->setter()}($objects);
            return $this;
        }

        $object->{$objectType->setter()}($this->setObject($objectType->className(), $value));

        return $this;
    }

    /**
     * @param Type\Object $objectType
     * @param array $values
     *
     * @return Object[]
     */
    private function processDeserializeTypeObjectCollection(Type\Object $objectType, array $values)
    {
        $objects = [];
        foreach ($values as $key => $object) {
            $objects[$key] = $this->setObject($objectType->className(), $object);
        }

        return $objects;
    }

    /**
     * @param Type\Handler $handler
     * @param Object $object
     * @param mixed $value
     *
     * @return Json
     */
    private function processDeserializeTypeHandler(Type\Handler $handler, $object, $value): self
    {
        $handler->deserializer()->deserialize($object, $value);

        return $this;
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
     * @return array
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
     * @param array $data
     * @param ObjectInterface $attribute
     *
     * @return array
     */
    private function processSerializeForType(Type $type, $object, $data, $attribute)
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
     * @param array $data
     * @param string $attribute
     *
     * @return mixed
     */
    private function processSerializeTypeMethod(Type\Method $method, $object, $data, string $attribute)
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
     * @param array $data
     * @param string $attribute
     *
     * @return array
     */
    private function processSerializeTypeObject(Type\Object $objectType, $object, $data, string $attribute)
    {
        if ($objectType->isCollection() === true) {
            return $this->processSerializeTypeObjectCollection($objectType, $object, $data, $attribute);
        }

        return $this->setArrayAndCheckNull($data, $object->{$objectType->getter()}(), $attribute);
    }

    /**
     * @param Type\Object $objectType
     * @param Object $object
     * @param array $data
     * @param string $attribute
     *
     * @return array
     */
    private function processSerializeTypeObjectCollection(
        Type\Object $objectType,
        $object,
        array $data,
        string $attribute
    ) {
        foreach ($object->{$objectType->getter()}() as $key => $subObject) {
            $data = $this->setArrayAndCheckNullWithKey($data, $subObject, $key, $attribute);
        }

        return $data;
    }

    /**
     * @param array $data
     * @param Object $object
     * @param string $attribute
     * @return array
     */
    private function setArrayAndCheckNull($data, $object, $attribute)
    {
        $value = $this->setArray($object);

        if ($this->checkNullForAttribute($value, $attribute) === false) {
            $data[$attribute]= $value;
        }

        return $data;
    }

    /**
     * @param array $data
     * @param Object $object
     * @param mixed $key
     * @param string $attribute
     * @return array
     */
    private function setArrayAndCheckNullWithKey($data, $object, $key, $attribute)
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
     * @param array $data
     * @param string $attribute
     *
     * @return mixed
     */
    private function processSerializeTypeHandler(Type\Handler $handler, $object, $data, string $attribute)
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
