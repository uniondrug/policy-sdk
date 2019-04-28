<?php

namespace Uniondrug\PolicySdk\Sdks\AiBao\Modules;

trait IdCheck
{
    public function idCheck(array $post)
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
                "applicantInfo" => [
                    "idNo" => $post['insurantIdNo'],
                    "idType" => $this->convertIdentifyType($post['insurantIdType']),
                    "idName" => $post['insurantIdName'],
                    "mobile" => $post['insurantMobile'],
                    "applicantNation" => $post['insurantNation'] ?? '',
                    "applicantAddress" => $post['insurantAddress'] ?? '',
                    "applicantIssuer" =>$post['insurantIssuer'] ?? '',
                    "applicantCertiStartDat" => $post['insurantCertiStartDat'] ?? '',
                    "applicantCertiEndDate" => $post['insurantCertiEndDate'] ?? '',
                ],
                "insuredInfo" => [
                    "insuredNation" => $post['insuredNation'] ?? '',
                    "insuredAddress" => $post['insuredAddress'] ?? '',
                    "insuredIssuer" =>$post['insuredIssuer'] ?? '',
                    "insuredCertiStartDat" => $post['insuredCertiStartDat'] ?? '',
                    "insuredCertiEndDate" => $post['insuredCertiEndDate'] ?? '',
                ]
            ],
        ];
        $postJson = json_encode($postData, JSON_UNESCAPED_UNICODE);
        $this->logger->idCheck()->info("保司请求报文:" . $postJson);
        $header = ['Content-Type: application/json'];
        $url = $this->setUrl('100080');
        try {
            $result = $this->curl_https($url, $postJson, $header, __FUNCTION__,'120');
        } catch (\Exception $e) {
            return $this->withError($e->getMessage());
        }
        $this->logger->idCheck()->info("保司响应报文:" . $result);
        $resultObj = json_decode($result,true);
        return $resultObj;
    }
}