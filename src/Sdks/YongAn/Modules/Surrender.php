<?php

namespace Uniondrug\PolicySdk\Sdks\YongAn\Modules;

trait Surrender
{
    public function surrender(array $post)
    {
        $date = date('Y-m-d\TH:i:s+08:00');
        $postData = ['arg0' => [
            "user" => $this->config->user,
            "password" => $this->config->password,
            "CCardBsnsTyp" => $this->config->salesSource,
            "issuedate" => $date,
            "policyno" => $post['policyNo'],
        ]];
        $url = $this->config->surrender;
        $client = new \nusoap_client($url, 'wsdl');
        $client->soap_defencoding = 'utf-8';
        $client->decode_utf8 = false;
        $client->xml_encoding = 'utf-8';
        $this->logger->surrender()->info("保司请求报文:" . json_encode($postData, JSON_UNESCAPED_UNICODE));
        $resultObj = $client->call('cancelapprequest', $postData);
        $this->logger->surrender()->info("保司响应报文:" . json_encode($resultObj, JSON_UNESCAPED_UNICODE));
        if ($err = $client->getError()) {
            return $this->withError($err);
        }
        $returnObj = $resultObj['return'];
        /*
         * 投保失败
         */
        if ($returnObj['flag']) {
            return $this->withError($returnObj['reason']);
        }
        $data = [
            'policyNo' => $post['policyNo'],
            'transTime' => $resultObj->transTime ?: date("Y-m-d H:i:s")
        ];
        return $this->withData($data);
    }
}