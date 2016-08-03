<?php

declare(strict_types=1);

namespace Blackprism\Serializer\Configuration\Type\Collection;

use Blackprism\Serializer\Configuration\TypeInterface;

/**
 * Object
 *
 * @property string $setter
 * @property string $getter
 */
final class IdentifiedObject implements TypeInterface
{
    /**
     * @var string
     */
    private $setter;

    /**
     * @var string
     */
    private $getter;

    /**
     * @param string $setter
     * @param string $getter
     */
    public function __construct(string $setter, string $getter)
    {
        $this->setter = $setter;
        $this->getter = $getter;
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
