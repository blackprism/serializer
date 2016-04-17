<?php

declare(strict_types=1);

namespace Blackprism\Demo\Entity;

/**
 * Country
 */
class Country
{

    /**
     * @var string
     */
    private $code = '';

    /**
     * @var string
     */
    private $name = '';

    /**
     * @var City
     */
    private $city = null;

    /**
     * @param string $code
     */
    public function setCode(string $code)
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param City $city
     */
    public function cityIs(City $city)
    {
        $this->city = $city;
    }

    /**
     * @return City|null
     */
    public function getCity()
    {
        return $this->city;
    }
}
