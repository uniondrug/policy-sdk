<?php
require_once __DIR__ . '/../vendor/autoload.php';

$container = new Uniondrug\PolicySdk\Container(dirname(__DIR__));

$instance = $container->get('policySDK')->instance("HX");

$instance->setConfig([
    'url' => '122121'
]);

echo $instance->insure([]);