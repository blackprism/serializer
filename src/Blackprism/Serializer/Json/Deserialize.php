<?php

declare(strict_types=1);

namespace Blackprism\Serializer\Json;

use Blackprism\Serializer\Configuration;
use Blackprism\Serializer\Configuration\Blackhole;
use Blackprism\Serializer\Configuration\Type;
use Blackprism\Serializer\DeserializerInterface;
use Blackprism\Serializer\Exception\InvalidJson;
use Blackprism\Serializer\Exception\MissingIdentifierAttribute;
use Blackprism\Serializer\Exception\UndefinedIdentifierAttribute;
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
     * @param string $serialized
     *
     * @return mixed
     * @throws InvalidJson
     */
    private function decodeJson(string $serialized)
    {
        $objectsAsArray = json_decode($serialized, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidJson(json_last_error_msg());
        }

        return $objectsAsArray;
    }

    /**
     * Deserialize string with class as the root object
     *
     * @param string $serialized
     * @param ClassName $className
     *
     * @return object|object[mixed]
     * @throws InvalidJson
     * @throws UndefinedIdentifierAttribute
     * @throws MissingIdentifierAttribute
     */
    public function deserialize(string $serialized, ClassName $className = null)
    {
        $objectsAsArray = $this->decodeJson($serialized);

        if ($className !== null) {
            return $this->setObjectForClass($className, $objectsAsArray);
        }

        return $this->setObject($objectsAsArray);
    }

    /**
     * Deserialize string with class as the root object
     *
     * @param string $serialized
     * @param ClassName $className
     *
     * @return object[mixed]
     * @throws InvalidJson
     * @throws UndefinedIdentifierAttribute
     * @throws MissingIdentifierAttribute
     */
    public function deserializeCollection(string $serialized, ClassName $className)
    {
        $objectsAsArray = $this->decodeJson($serialized);

        $objects = [];
        foreach ($objectsAsArray as $attribute => $object) {
            $objects[$attribute] = $this->setObjectForClass($className, $object);
        }

        return $objects;
    }

    /**
     * Create class object with data
     *
     * @param mixed[string] $data
     *
     * @return object|object[mixed]
     * @throws UndefinedIdentifierAttribute
     * @throws MissingIdentifierAttribute
     */
    private function setObject(array $data)
    {
        $identifierAttribute = $this->configuration->getIdentifierAttribute();

        if ($identifierAttribute === null) {
            throw new UndefinedIdentifierAttribute();
        }

        // We found an identifier attribute, $data is a json object
        if (isset($data[$identifierAttribute]) === true) {
            return $this->setObjectForObjectData($identifierAttribute, $data);
        }

        // We don't found an identifier attribute, $data is a collection
        return $this->setObjectForCollectionData($data);
    }

    /**
     * @param string $identifierAttribute
     * @param array $data
     *
     * @return mixed
     */
    private function setObjectForObjectData(string $identifierAttribute, array $data)
    {
        $configurationObject = $this->configuration
            ->getConfigurationObjectForIdentifier($data[$identifierAttribute]);
        $className = $configurationObject->getClassName();
        $fqdnClass = $className->getValue();
        $object = new $fqdnClass();

        foreach ($data as $attribute => $value) {
            if ($attribute === $identifierAttribute) {
                continue;
            }

            $type = $configurationObject->getTypeForAttribute($attribute);
            $this->processDeserializeForType($type, $object, $value);
        }

        return $object;
    }


    /**
     * @param mixed[] $data
     *
     * @return object[]
     * @throws MissingIdentifierAttribute
     * @throws UndefinedIdentifierAttribute
     */
    private function setObjectForCollectionData(array $data)
    {
        $objects = [];
        foreach ($data as $attribute => $object) {
            if (is_array($object) === false) {
                throw new MissingIdentifierAttribute();
            }

            $objects[$attribute] = $this->setObject($object);
        }

        return $objects;
    }

    /**
     * Create $className object with data
     *
     * @param ClassName $className
     * @param mixed[string] $data
     *
     * @return object|object[mixed]
     */
    private function setObjectForClass(ClassName $className, array $data)
    {
        $fqdnClass = $className->getValue();
        $object = new $fqdnClass();

        foreach ($data as $attribute => $value) {
            $configurationObject = $this->configuration->getConfigurationObjectForClass($className);
            $type = $configurationObject->getTypeForAttribute($attribute);

            $this->processDeserializeForType($type, $object, $value);
        }

        return $object;
    }

    /**
     * @param Configuration\TypeInterface $type
     * @param object $object
     * @param mixed $value
     */
    private function processDeserializeForType(Configuration\TypeInterface $type, $object, $value)
    {
        if ($type instanceof Type\Method) {
            $this->processDeserializeTypeMethod($type, $object, $value);
        } elseif ($type instanceof Type\Object) {
            $this->processDeserializeTypeObject($type, $object, $value);
        } elseif ($type instanceof Type\Collection\Object) {
            $this->processDeserializeTypeCollectionObject($type, $object, $value);
        } elseif ($type instanceof Type\IdentifiedObject) {
            $this->processDeserializeTypeIdentifiedObject($type, $object, $value);
        } elseif ($type instanceof Type\Collection\IdentifiedObject) {
            $this->processDeserializeTypeCollectionIdentifiedObject($type, $object, $value);
        } elseif ($type instanceof Type\Handler) {
            $this->processDeserializeTypeHandler($type, $object, $value);
        }
    }

    /**
     * @param Type\Method $method
     * @param object $object
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
     * @param object $object
     * @param mixed $value
     *
     * @return Deserialize
     */
    private function processDeserializeTypeObject(Type\Object $objectType, $object, $value): self
    {
        $object->{$objectType->setter()}($this->setObjectForClass($objectType->className(), $value));

        return $this;
    }

    /**
     * @param Type\Collection\Object $objectType
     * @param object $object
     * @param array $values
     *
     * @return Deserialize
     */
    private function processDeserializeTypeCollectionObject(Type\Collection\Object $objectType, $object, array $values)
    {
        $objects = [];
        foreach ($values as $key => $objectFromValue) {
            $objects[$key] = $this->setObjectForClass($objectType->className(), $objectFromValue);
        }

        $object->{$objectType->setter()}($objects);

        return $this;
    }

    /**
     * @param Type\IdentifiedObject $objectType
     * @param object $object
     * @param mixed $value
     *
     * @return Deserialize
     */
    private function processDeserializeTypeIdentifiedObject(Type\IdentifiedObject $objectType, $object, $value): self
    {
        $identifierAttribute = $this->configuration->getIdentifierAttribute();

        if (isset($value[$identifierAttribute]) === false) {
            return $this;
        }

        $configurationObject = $this->configuration->getConfigurationObjectForIdentifier($value[$identifierAttribute]);

        if ($configurationObject instanceof Blackhole) {
            return $this;
        }

        $object->{$objectType->setter()}($this->setObjectForClass($configurationObject->getClassName(), $value));

        return $this;
    }

    /**
     * @param Type\Collection\IdentifiedObject $objectType
     * @param object $object
     * @param array $values
     *
     * @return Deserialize
     */
    private function processDeserializeTypeCollectionIdentifiedObject(
        Type\Collection\IdentifiedObject $objectType,
        $object,
        array $values
    ): self {
        $identifierAttribute = $this->configuration->getIdentifierAttribute();

        $objects = [];
        foreach ($values as $key => $objectFromValue) {
            if (isset($objectFromValue[$identifierAttribute]) === false) {
                continue;
            }

            $configurationObject =
                $this->configuration->getConfigurationObjectForIdentifier($objectFromValue[$identifierAttribute]);

            if ($configurationObject instanceof Blackhole) {
                continue;
            }

            $objects[$key] = $this->setObjectForClass($configurationObject->getClassName(), $objectFromValue);
        }

        $object->{$objectType->setter()}($objects);

        return $this;
    }

    /**
     * @param Type\Handler $handler
     * @param object $object
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
