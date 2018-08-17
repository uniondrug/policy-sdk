<?php
/**
 * Created by PhpStorm.
 * User: luzhouyu
 * Date: 18/8/17
 * Time: 上午11:55
 */

namespace Uniondrug\PolicySdk\Sdks\RenBaoJson\Modules;


trait Surrender
{
    public function surrender(array $post)
    {
        $postData = [
            'waterNo' => $post['waterNo'],
            'productCode' => $post['riskCode'],
            'rationType' => $post['rationType'],
            'withdrawdate' => $post['transTime'],   //  投保时间
            'policyNo' => $post['policyNo']
        ];
        $postJson = json_encode($postData, JSON_UNESCAPED_UNICODE);
        $this->logger->surrender()->info("保司请求报文:" . $postJson);
        $header = ['Content-Type: application/json'];
        try {
            $result = $this->curl_https($this->config->surrender, $postJson, $header, __FUNCTION__);
        } catch (\Exception $e) {
            return $this->withError($e->getMessage());
        }
        $this->logger->surrender()->info("保司响应报文:" . $result);
        $resultObj = json_decode($result);
        if ($resultObj->retCode != "00") {
            $msg = (!$resultObj->retCode) ? $resultObj->resultDTO->resultMess : $resultObj->returnMsg;
            return $this->withError($msg);
        }
        $data = [
            'policyNo' => $post['policyNo'],
            'transTime' => $resultObj->transTime ?: date("Y-m-d H:i:s")
        ];
        return $this->withData($data);
    }
}