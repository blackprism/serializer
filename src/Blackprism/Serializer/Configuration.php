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
     * @param ClassName $className
     * @param ObjectInterface $configurationObject
     *
     * @return Configuration
     */
    public function addConfigurationObject(ClassName $className, ObjectInterface $configurationObject): self
    {
        $this->objects[$className->getIdentifier()] = $configurationObject;

        return $this;
    }

    /**
     * @param ClassName $className
     *
     * @return ObjectInterface
     */
    public function getConfigurationObjectForClass(ClassName $className): ObjectInterface
    {
        if (isset($this->objects[$className->getIdentifier()]) === true) {
            return $this->objects[$className->getIdentifier()];
        }

        return new Blackhole();
    }
}
