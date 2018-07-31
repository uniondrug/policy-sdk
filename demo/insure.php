<?php

require_once __DIR__ . '/../vendor/autoload.php';

$policySdk = new Uniondrug\PolicySdk\PolicySdk();

$json = require_once __DIR__ . DIRECTORY_SEPARATOR . 'doc' . DIRECTORY_SEPARATOR . 'insure.php';

$instance = $policySdk->instance("YG");

$YAConfig = [
    "insure" => "http://tapi.yaic.com.cn/yaicservice/api/appservice?wsdl",
    "surrender" => "http://tapi.yaic.com.cn/yaicservice/api/cancelappservice?wsdl"
];

$YGConfig = [
    "insure" => "http://1.202.235.72:8082/ifp-TCYG/SyncInterface",
    "surrender" => "http://1.202.235.72:8082/ifp-TCYG/SyncInterface",
    "key" => "jtyugewruuknh"
];

$instance->setConfig($YGConfig);

$data = json_decode($json,true);

$result = $instance->insure($data);

var_dump($result);