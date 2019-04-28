<?php

namespace Uniondrug\PolicySdk\Sdks\AiBao\Modules;

trait InsureCheck
{
    public function insureCheck(array $post)
    {
        $postData = [
            'head' => [
                'transactionNo' => $this->createUniqueWaterNo(),
                'aiBaoTransactionNo' => '',
                'operator' => $this->config->operator,
                'timeStamp' => date('Y-m-d H:i:s',time()),
                'errorCode' => '0000',
                'errorMsg' =>  '成功',
            ],
            'body' => [
                'cityCode' => $post['cityCode'], //投保城市代码
                'licenseNoFlag' =>$post['licenseNoFlag'] , //新车未上牌标示 0-非新车 1-新车
                'licenseNo' => $post['licenseNo'], //车牌号(字母大写)
                'channelId' =>$this->config->channelId , //渠道编号
                'userId' => '',
            ],
        ];
        $postJson = json_encode($postData, JSON_UNESCAPED_UNICODE);
        $this->logger->insureCheck()->info("保司请求报文:" . $postJson);
        $header = ['Content-Type: application/json'];
        $url = $this->setUrl('100069');
        try {
            $result = $this->curl_https($url, $postJson, $header, __FUNCTION__,'120');
        } catch (\Exception $e) {
            return $this->withError($e->getMessage());
        }
        $this->logger->insureCheck()->info("保司响应报文:" . $result);
        $resultObj = json_decode($result,true);
        return $resultObj;

//        if ($resultObj->head->errorCode != "0000") {
//            return $this->withError($resultObj->head->errorMsg);
//        }
//        $data = [
//            'aiBaoTransactionNo' => $resultObj->head->aiBaoTransactionNo,
//            'cityCode' => $resultObj->body->cityCode,
//            'licenseNoFlag' => $resultObj->body->licenseNoFlag,
//            'licenseNo' => $resultObj->body->licenseNo,
//            'transactionNo' => $resultObj->head->transactionNo,
//        ];
//        return $this->withData($data);
    }
}