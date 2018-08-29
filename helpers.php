<?php

if (!function_exists('convert_encoding')) {
    function convert_encoding($xml, $to_encode = 'UTF-8')
    {
        $encoding = mb_detect_encoding($xml, array('ASCII', 'UTF-8', 'GB2312', 'GBK', 'BIG5'));
        $xml = mb_convert_encoding($xml, $to_encode, $encoding);
        return $xml;
    }
}

if (!function_exists('xml_to_array')) {
    function xml_to_array($xml, $to_encode = 'UTF-8')
    {
        $xml = convert_encoding($xml, $to_encode);
        $dataObj = simplexml_load_string($xml, null, LIBXML_NOCDATA);
        $dataObj = json_decode(str_replace("{}", '""', json_encode((array)$dataObj)), true);
        return $dataObj;
    }
}

if (!function_exists('xml_to_object')) {
    function xml_to_object($xml, $to_encode = 'UTF-8')
    {
        $xml = convert_encoding($xml, $to_encode);
        $dataObj = simplexml_load_string($xml, null, LIBXML_NOCDATA);
        $dataObj = json_decode(str_replace("{}", '""', json_encode((array)$dataObj)));
        return $dataObj;
    }
}

