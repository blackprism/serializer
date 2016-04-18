<?php

use Blackprism\Serializer\Mapper;
use Blackprism\Serializer\MapperHandler;

require_once 'vendor/autoload.php';
require_once 'src/Blackprism/Demo/config.php';

$json = json_encode(
    [
        'id' => 3,
        'name' => 'Palaiseau',
        'country' => [
            'code' => 'FRA',
            'name' => 'France',
            'city' => [
                'id' => 4,
                'name' => 'Palaiseau2'
            ],
            'unknown' => 'Toto'
        ],
        'countries' => [
            [
                'code' => 'FRA2',
                'name' => 'France2',
                'unknown' => 'Toto'
            ],
            [
                'code' => 'FRA3',
                'name' => 'France3',
                'city' => [
                    'id' => 5,
                    'name' => 'Palaiseau2France3',
                    'country' => [
                        'code' => 'FRA2',
                        'name' => 'France2',
                        'city' => [
                            'id' => 5,
                            'name' => 'Palaiseau22',
                            'unknown' => 'Toto'
                        ]
                    ]
                ]
            ],
            [
                'code' => 'FRA4',
                'name' => 'France4'
            ]
        ]
    ]
);


$start = microtime(true);

for ($i = 0; $i < 10000; $i++) {
    $jsonSerializer = new \Blackprism\Serializer\Json($configuration);
    $city = $jsonSerializer->deserialize($json, new \Blackprism\Serializer\Value\ClassName(\Blackprism\Demo\Entity\City::class));
}
$end = microtime(true);

$jsonSerializer->serialize($city);

var_dump($city);

$array = $jsonSerializer->serialize($city);

var_dump($array);

echo "Test2 Deserialize : " . ($end - $start) . "\n";

