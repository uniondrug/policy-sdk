<?php
/**
 * Created by PhpStorm.
 * User: yaolian
 * Date: 2018/11/7
 * Time: 22:38
 */
namespace Uniondrug\PolicySdk\Sdks\HuaXia\Modules;

trait QueryClaim
{
    public function queryClaim(array $post)
    {
        $postData['Head'] = [
            "ServiceID"=> "medUnion002",
            "TransNo"=> $post['transNo'],
            "FromSystemkey"=> "zt",
            "TransDate"=> date("Y-m-d"),
            "TransTime"=> date("H-i-s"),
        ];

        $postData['Body'] = [
            "BillNo"=> $post['billNo'],
            "BatchNo"=> $post['batchNo'],
        ];
        $postJson = json_encode($postData, JSON_UNESCAPED_UNICODE);

        $this->logger->queryClaim()->info("保司请求报文:" . $postJson);
        $header[] = 'Content-Type: application/json';
        $header[] = 'MethodName: llylClaim';
        $timeout = 60;
        try {
            $result = $this->curl_https($this->config->queryClaim, $postJson, $header, __FUNCTION__ ,$timeout);
        } catch (\Exception $e) {
            return $this->withError($e->getMessage());
        }
        $this->logger->queryClaim()->info("保司响应报文:" . $result);
        $resultObj = json_decode($result);
        if ($resultObj->Head->ResultCode != "0") {
            return $this->withError($resultObj->Head->ResultMessage);
        }
        $data = [
            'BillNo' => $resultObj->Body->BillNo,
            'BatchNo' => $resultObj->Body->BatchNo,
            'TransNo' => $resultObj->Body->TransNo,
            'BillInfos' => $resultObj->Body->BillInfos,
        ];
        return $this->withData($data);
    }


}