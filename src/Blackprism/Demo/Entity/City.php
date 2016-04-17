<?php

declare(strict_types=1);

namespace Blackprism\Demo\Entity;

/**
 * City
 */
class City
{
    /**
     * @var int
     */
    private $id = 0;

    /**
     * @var string
     */
    private $name = '';

    /**
     * @var Country
     */
    private $country = null;

    /**
     * @var string
     */
    private $countryCode = '';

    /**
     * @var array
     */
    private $countries = [];

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
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
     * @param string $countryCode
     */
    public function setCountryCode(string $countryCode)
    {
        $this->countryCode = $countryCode;
    }

    /**
     * @return string
     */
    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    /**
     * @param Country $country
     */
    public function countryIs(Country $country)
    {
        $this->country = $country;
    }

    /**
     * @return Country|null
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param Country[] $countries
     */
    public function countriesAre(array $countries)
    {
        $this->countries = $countries;
    }

    /**
     * @return Country[]
     */
    public function getCountries(): array
    {
        return $this->countries;
    }
}
