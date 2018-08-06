<?php

namespace Uniondrug\PolicySdk\Sdks\HuaXia\Modules;

trait Surrender
{
    public function surrender(array $post)
    {
        $postData = [
            'cooperation' => 'tongcheng',
            'waterNo' => $post['waterNo'],
            'policyNo' => $post['policyNo'],
            'productCode' => $post['rationType'],
            'cancelReason' => '接口保单注销'
        ];
        /*
        * 组装报文
        */
        $postData = $this->createParams($postData);
        $this->logger->surrender()->info("保司请求报文:" . json_encode($postData, JSON_UNESCAPED_UNICODE));
        $header = ['Content-Type: application/x-www-form-urlencoded'];
        $postQuery = http_build_query($postData);
        try {
            $result = $this->curl_https($this->config->surrender, $postQuery, $header, __FUNCTION__);
        } catch (\Exception $e) {
            return $this->withError($e->getMessage());
        }
        $this->logger->surrender()->info("保司响应报文:" . $result);
        $resultObj = json_decode($result);
        if ($resultObj->retCode != "00") {
            $msg = $resultObj->retDesc ?: $resultObj->returnMsg;
            return $this->withError($msg);
        }
        $data = [
            'policyNo' => $post['policyNo'],
            'transTime' => $resultObj->transTime ?: date("Y-m-d H:i:s")
        ];
        return $this->withData($data);
    }
}