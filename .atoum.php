<?php

use mageekguy\atoum\reports;
use mageekguy\atoum\writers\std;

$script->addDefaultReport();
$script->enableBranchAndPathCoverage();

$coverage = new reports\coverage\html();
$coverage->addWriter(new std\out());
$coverage->setOutPutDirectory('tests/coverage');
$runner->addReport($coverage);

$telemetry = new reports\telemetry();
$telemetry->setProjectName('blackprism/serializer');
$telemetry->addWriter(new std\out());
$runner->addReport($telemetry);
