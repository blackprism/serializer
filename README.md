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

### Now, you can serialize an object

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

Output is:
```json
{
  "name": "Palaiseau",
  "country": {
    "name": "France"
  }
}
```

### And unserialize a json
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

Output is:
```php
class City {
  private $name =>
    string(9) "Palaiseau"
  private $country =>
      class Country {
        private $name =>
        string(6) "France"
      }
}
```

## Custom handler

From previous sample, you change a bit the configuration to this:
```php
$configurationObject
    ->attributeUseMethod('name', 'setName', 'getName')
    ->attributeUseHandler(
		'country',
		new class implements Configuration\Type\HandlerDeserializerInterface {
            public function deserialize($object, $value)
            {
                $country = new Country();
                $country->setName($value['name']);
                $object->countryIs($country);
                $object->setName($object->getName() . ' (' . $country->getName() . ')');
            }
        },
        new class implements Configuration\Type\HandlerSerializerInterface {
            public function serialize($object)
            {
                $country = $object->getCountry();
                return ['name' => $country->getName() . '#' . spl_object_hash($country)];
            }
        }
	);
```

## You can use it to serialize/unserialize for a noSQL
When using a nosql you often add a property to your document to specify what type of document it is, for example:
```json
{
  "type": "city",
  "name": "Palaiseau"
}
```

#### Configuration for identified document
```php
use Blackprism\Serializer\Configuration;
use Blackprism\Serializer\Value\ClassName;

$configuration = new Configuration();
$configuration->identifierAttribute('type'); // name the property which contain the type of document

$configurationObject = new Configuration\Object(new ClassName(City::class));
$configurationObject
    ->attributeUseMethod('name', 'setName', 'getName')
    ->attributeUseIdentifiedObject('country', 'countryIs', 'getCountry') // You don't need to tell which class of object is
    ->registerToConfigurationWithIdentifier($configuration, 'city'); // Register the configuration with an identifier

$configurationObject = new Configuration\Object(new ClassName(Country::class));
$configurationObject
    ->attributeUseMethod('name', 'setName', 'getName')
    ->registerToConfigurationWithIdentifier($configuration, 'country'); // Register the configuration with an identifier

```

#### Now, you can serialize to a typed object json

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

Output is:
```json
{
  "type": "city",
  "name": "Palaiseau",
  "country": {
    "type": "country",
    "name": "France"
  }
}
```

#### And unserialize a typed object json
```php
use Blackprism\Serializer\Json;

$json = '{
          "type": "city",
          "name": "Palaiseau",
          "country": {
            "type": "country",
            "name": "France"
          }
        }';

$jsonDeserializer = new Json\Deserialize($configuration);
$city = $jsonDeserializer->deserialize($json);

print_r($city);
```

Output is:
```php
class City {
  private $name =>
    string(9) "Palaiseau"
  private $country =>
      class Country {
        private $name =>
        string(6) "France"
      }
}
```

## Benchmark

For 100,000 iterations :

Library               | Serialize time | Serialize memory | Deserialize time | Deserialize Memory
----------------------|----------------|------------------|------------------|-------------------
       JMS Serializer |      1.951 sec |          1537 KB |        1.829 sec |            1547 KB
Symfony Serializer    |      0.210 sec |           486 KB |        0.298 sec |             488 KB
Blackprism Serializer |      0.352 sec |           464 KB |        0.311 sec |             457 KB

Test protocol can be found on [Serializer Benchmark](https://github.com/blackprism/serializer-benchmark)

## Conclusion

As you can see, Blackprism Serializer isn't really the fastest, but it has a quick and simple configuration with very good performance, almost the same as Symfony Serializer which has a more complex configuration.
