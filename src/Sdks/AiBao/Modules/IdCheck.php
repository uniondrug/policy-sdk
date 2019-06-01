<?php

namespace Uniondrug\PolicySdk\Sdks\AiBao\Modules;

trait IdCheck
{
    public function idCheck(array $post)
    {
        $postData = [
            'head' => $this->getHeader($post),
            'body' => [
                "applicantInfo" => [
                    "idNo" => $post['applicantIdNo'],
                    "idType" => $this->convertIdentifyType($post['applicantIdType']),
                    "idName" => $post['applicantName'],
                    "mobile" => $post['applicantMobile'],
                    "applicantNation" => $post['applicantNation'] ?? '',
                    "applicantAddress" => $post['applicantAddress'] ?? '',
                    "applicantIssuer" =>$post['applicantIssuer'] ?? '',
                    "applicantCertiStartDat" => $post['applicantCertiStartDat'] ?? '',
                    "applicantCertiEndDate" => $post['applicantCertiEndDate'] ?? '',
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
        return $this->returnRes($resultObj);
    }
}