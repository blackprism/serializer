<?php

declare(strict_types=1);

namespace Blackprism\Serializer\Json;

use Blackprism\Serializer\Configuration;
use Blackprism\Serializer\Configuration\Type;
use Blackprism\Serializer\DeserializerInterface;
use Blackprism\Serializer\Exception\InvalidJson;
use Blackprism\Serializer\Value\ClassName;

/**
 * Deserialize
 *
 * @property Configuration $configuration
 */
class Deserialize implements DeserializerInterface
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
     * @return Deserialize
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
     * @return Deserialize
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
     * @return Deserialize
     */
    private function processDeserializeTypeHandler(Type\Handler $handler, $object, $value): self
    {
        $handler->deserializer()->deserialize($object, $value);

        return $this;
    }
}
