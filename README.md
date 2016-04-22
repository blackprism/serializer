# Serializer

**A fast and simple json serializer library for PHP 7.0+**

Serializer has been designed to be very fast and with low memory usage.

## How to use it ?

According you have these objects you want to serialize/unserialize
```php
class City
{
    private $name = '';
    private $country = null;

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function countryIs(Country $country)
    {
        $this->country = $country;
    }

    public function getCountry()
    {
        return $this->country;
    }
}

class Country
{
    private $name = '';

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
```

First you need to setup the configuration, pure PHP, no yml, no xml.
Faster and lighter.
```php
use Blackprism\Serializer\Configuration;
use Blackprism\Serializer\Value\ClassName;

$configuration = new Configuration();

$configurationObject = new Configuration\Object(new ClassName(City::class));
$configurationObject
    ->attributeUseMethod('name', 'setName', 'getName')
    ->attributeUseObject('country', new ClassName(Country::class), 'countryIs', 'getCountry')
    ->registerToConfiguration($configuration);

$configurationObject = new Configuration\Object(new ClassName(Country::class));
$configurationObject
    ->attributeUseMethod('name', 'setName', 'getName')
    ->registerToConfiguration($configuration);

```

### Now, you can serialize an object.
```php
use Blackprism\Serializer\Json;

$country = new Country();
$country->setName('France');

$city = new City();
$city->setName('Palaiseau');
$city->countryIs($country);

$jsonSerializer = new Json\Serialize($configuration);
$citySerialized = $jsonSerializer->serialize($city);

echo $citySerialized;
```

Output is :
```json
{
  "name": "Palaiseau",
  "country": {
    "name": "France"
  }
}
```

### And unserialize a json.
```php
use Blackprism\Serializer\Json;

$json = '{
          "name": "Palaiseau",
          "country": {
            "name": "France"
          }
        }';

$jsonDeserializer = new Json\Deserialize($configuration);
$city = $jsonDeserializer->deserialize($json, new ClassName(City::class));

print_r($city);
```

Output is :
```php
class City#18 (2) {class City#18 (2) {
  private $name =>
  string(9) "Palaiseau"
  private $country =>
  class Country#19 (1) {
    private $name =>
    string(6) "France"
  }
}

  private $name =>
  string(9) "Palaiseau"
  private $country =>
  class Country#19 (1) {
    private $name =>
    string(6) "France"
  }
}
```
