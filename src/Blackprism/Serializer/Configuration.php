<?php

declare(strict_types=1);

namespace Blackprism\Serializer;

use Blackprism\Serializer\Configuration\Blackhole;
use Blackprism\Serializer\Configuration\ObjectInterface;
use Blackprism\Serializer\Value\ClassName;

/**
 * Configuration
 *
 * @property ObjectInterface[string] $objects
 */
final class Configuration
{
    /**
     * @var ObjectInterface[string]
     */
    private $objects;

    /**
     * @param string $class
     * @param ObjectInterface $configurationObject
     *
     * @return Configuration
     */
    public function addConfigurationObject(string $class, ObjectInterface $configurationObject): self
    {
        $this->objects[$class] = $configurationObject;
        return $this;
    }

    /**
     * @param ClassName $class
     *
     * @return ObjectInterface
     */
    public function getConfigurationObjectForClass(ClassName $class): ObjectInterface
    {
        if (isset($this->objects[$class->getIdentifier()]) === true) {
            return $this->objects[$class->getIdentifier()];
        }

        return new Blackhole();
    }
}
