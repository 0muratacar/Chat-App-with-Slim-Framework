<?php
require 'vendor/autoload.php';

use OpenApi\Generator;

$openapi = Generator::scan([__DIR__ . '/src/Controllers', __DIR__ . '/src/Models']);

header('Content-Type: application/json');
echo $openapi->toJson();