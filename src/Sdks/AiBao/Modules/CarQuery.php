<?php

namespace Uniondrug\PolicySdk\Sdks\AiBao\Modules;

trait CarQuery
{
    public function carQuery(array $post)
    {
        $postData = [
            'head' => [
                'transactionNo' => $post['transactionNo'],
                'aiBaoTransactionNo' => $post['aiBaoTransactionNo'],
                'operator' => $this->config->operator,
                'timeStamp' => date('Y-m-d H:i:s',time()),
                'errorCode' => '0000',
                'errorMsg' =>  '成功',
            ],
            'body' => [
                'channelId' => $this->config->channelId, //渠道编号
                'userId' => '', //可不传
                'seachType' => $post['seachType'] ?? '1', //车架号查询传1；厂牌型号查询传5
                'searchKey' => $post['searchKey'], //当seachType传1时，该值内容为车架号(字母大写)；当seachType传5时，该值内容为厂牌型号（或要查询的车型）
                'yearPatterns' => $post['searchKey'] ?? '', //年款，如果传入该值会根据年款进行筛选，例如2016
                'exhaustScales' => $post['exhaustScales'] ?? '', //排量或功率，根据排量或功率进行筛选，例如：1.4T
                'getPage' => $post['exhaustScales'] ?? '', //如果存在分页的情况，查询第二页或者第N页使用
            ],
        ];
        $postJson = json_encode($postData, JSON_UNESCAPED_UNICODE);
        $this->logger->carQuery()->info("保司请求报文:" . $postJson);
        $header = ['Content-Type: application/json'];
        $url = $this->setUrl('100070');
        try {
            $result = $this->curl_https($url, $postJson, $header, __FUNCTION__,'120');
        } catch (\Exception $e) {
            return $this->withError($e->getMessage());
        }
        $this->logger->carQuery()->info("保司响应报文:" . $result);
        $resultObj = json_decode($result,true);
        return $resultObj;
/*        if ($resultObj->head->errorCode != "0000") {
            return $this->withError($resultObj->head->errorMsg);
        }
        $data = [
            'aiBaoTransactionNo' => $resultObj->head->aiBaoTransactionNo,
            'transactionNo' => $resultObj->head->transactionNo,
            'mainInfo' => $resultObj->body,
        ];
        return $this->withData($data);*/
    }
}