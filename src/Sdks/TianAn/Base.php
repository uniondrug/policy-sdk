<?php

namespace Uniondrug\PolicySdk\Sdks\TianAn;

use Uniondrug\PolicySdk\Sdk;
use Uniondrug\PolicySdk\Sdks\TianAn\Modules\Insure;
use Uniondrug\PolicySdk\Sdks\TianAn\Modules\Surrender;

class Base extends Sdk
{
    /*
     * 投保
     */
    use Insure;

    /*
     * 退保
     */
    use Surrender;

    protected function createRequestHead($cooperation, $token = null)
    {
        $nonce = strtoupper(md5(uniqid(mt_rand(), true)));
        $timestamp = time();
        $requestHead = array(
            "cooperation" => $cooperation,
            "nonce" => $nonce,
            "sign" => $this->getSign($nonce, $timestamp, $token),
            "timestamp" => $timestamp,
            "tradeNo" => $timestamp . rand(10000, 99999),
            "tradeDate" => date("Y-m-d H:i:s"),
        );
        return $requestHead;
    }

    protected function getSign($nonce, $timestamp, $token)
    {
        $data = array($token, $nonce, $timestamp);
        sort($data, SORT_STRING);
        $sign_orign = implode('', $data);
        return sha1($sign_orign);
    }
}