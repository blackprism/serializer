<?php

declare(strict_types=1);

namespace Blackprism\Serializer;

use Blackprism\Serializer\Configuration\Type;
use Blackprism\Serializer\Exception\InvalidJson;
use Blackprism\Serializer\Exception\InvalidObject;

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
     * @param string $class
     *
     * @return Object
     * @throws InvalidJson
     */
    public function deserialize(string $serialized, string $class)
    {
        $objectsAsArray = json_decode($serialized, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidJson(json_last_error_msg());
        }

        return $this->setObject($class, $objectsAsArray);
    }

    /**
     * Create class object with data
     *
     * @param string $class
     * @param array  $data
     *
     * @return Object
     */
    private function setObject(string $class, array $data)
    {
        $object = new $class();

        /**
         * @var string $attribute
         * @var mixed $value
         */
        foreach ($data as $attribute => $value) {
            $configurationObject = $this->configuration->getConfigurationObjectForClass($class);
            $type = $configurationObject->getTypeForAttribute($attribute);

            if ($type instanceof Type\Method) {
                $this->processDeserializeTypeMethod($type, $object, $value);
            } elseif ($type instanceof Type\Object) {
                $this->processDeserializeTypeObject($type, $object, $value);
            } elseif ($type instanceof Type\Handler) {
                $this->processDeserializeTypeHandler($type, $object, $value);
            }
        }

        return $object;
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
            $objects = [];
            foreach ($value as $key => $objectData) {
                $objects[$key] = $this->setObject($objectType->className(), $objectData);
            }

            $object->{$objectType->setter()}($objects);
        } else {
            $object->{$objectType->setter()}(
                $this->setObject($objectType->className(), $value)
            );
        }

        return $this;
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

        $configurationObject = $this->configuration->getConfigurationObjectForClass(get_class($object));

        foreach ($configurationObject->getAttributes() as $attribute) {
            $type = $configurationObject->getTypeForAttribute($attribute);

            if ($type instanceof Type\Method) {
                $data = $this->processSerializeTypeMethod($type, $object, $data, $attribute);
            } elseif ($type instanceof Type\Object) {
                $data = $this->processSerializeTypeObject($type, $object, $data, $attribute);
            } elseif ($type instanceof Type\Handler) {
                $data = $this->processSerializeTypeHandler($type, $object, $data, $attribute);
            }
        }

        return $data;
    }

    /**
     * @param Type\Method $method
     * @param Object $object
     * @param mixed $data
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
     * @param mixed $data
     * @param string $attribute
     *
     * @return mixed
     */
    private function processSerializeTypeObject(Type\Object $objectType, $object, $data, string $attribute)
    {
        if ($objectType->isCollection() === true) {
            foreach ($object->{$objectType->getter()}() as $key => $subObject) {
                $value = $this->setArray($subObject);

                if ($this->checkNullForAttribute($value, $key) === false) {
                    $data[$attribute][$key] = $value;
                }
            }
        } else {
            $value = $this->setArray($object->{$objectType->getter()}());

            if ($this->checkNullForAttribute($value, $attribute) === false) {
                $data[$attribute] = $value;
            }
        }

        return $data;
    }

    /**
     * @param Type\Handler $handler
     * @param Object $object
     * @param mixed $data
     * @param string attribute
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
