<?php

require_once __DIR__ . '/vendor/autoload.php';

$policySdk = new Uniondrug\PolicySdk\PolicySdk();

$json = require_once __DIR__ . DIRECTORY_SEPARATOR . 'doc' . DIRECTORY_SEPARATOR . 'insure.php';

$instance = $policySdk->instance("HX");

$instance->setConfig([
    'insure' => 'http://112.74.229.197:8080/adpt/tcheng/policy/uw',
    'surrender' => 'http://112.74.229.197:8080/adpt/tcheng/policy/cancel',
    'key' => 'a12dadf_12asdfadsfaf213tongchgADq1da249jbb_10AF1As',
]);

$data = json_decode($json,true);

$result = $instance->insure($data);

var_dump($result);