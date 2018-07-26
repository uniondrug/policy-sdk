<?php
require_once __DIR__ . '/../vendor/autoload.php';

$container = new Uniondrug\PolicySdk\Container(dirname(__DIR__));

$instance = $container->instance("HX");

$instance->setConfig([
    'url' => '122121'
]);

echo $instance->insure([]);