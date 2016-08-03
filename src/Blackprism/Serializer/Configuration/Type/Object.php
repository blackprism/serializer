<?php

declare(strict_types=1);

namespace Blackprism\Serializer\Configuration\Type;

use Blackprism\Serializer\Configuration\TypeInterface;
use Blackprism\Serializer\Value\ClassName;

/**
 * Object
 *
 * @property ClassName $className
 * @property string $setter
 * @property string $getter
 */
final class Object implements TypeInterface
{
    /**
     * @var ClassName
     */
    private $className;

    /**
     * @var string
     */
    private $setter;

    /**
     * @var string
     */
    private $getter;

    /**
     * @param ClassName $className
     * @param string $setter
     * @param string $getter
     */
    public function __construct(ClassName $className, string $setter, string $getter)
    {
        $this->className = $className;
        $this->setter = $setter;
        $this->getter = $getter;
    }

    /**
     * @return ClassName
     */
    public function className(): ClassName
    {
        return $this->className;
    }

    /**
     * @return string
     */
    public function setter(): string
    {
        return $this->setter;
    }

    /**
     * @return string
     */
    public function getter(): string
    {
        return $this->getter;
    }
}
