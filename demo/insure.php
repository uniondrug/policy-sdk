<?php

error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';

$policySdk = new Uniondrug\PolicySdk\PolicySdk();

$instance = $policySdk->instance("YANGGUANG");

$YGConfig = [
    "insure" => "http://1.202.235.72:8082/ifp-TCYG/SyncInterface",
    "surrender" => "http://1.202.235.72:8082/ifp-TCYG/SyncInterface",
    "token" => "jtyugewruuknh"
];

$instance->setConfig($YGConfig);

$data = ['policyNo'=>'86242985438308990977'];

$result = $instance->epolicy($data);