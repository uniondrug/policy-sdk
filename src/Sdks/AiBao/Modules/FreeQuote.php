<?php

namespace Uniondrug\PolicySdk\Sdks\AiBao\Modules;

trait FreeQuote
{
    public function freeQuote(array $post)
    {
        $postData = [
            'head' => $this->getHeader($post),
            'body' => [
                "mainInfo" => [
                    "busiStartDate" => $post['bizBeginDate'] ?? '',
                    "busiEndDate" => $post['bizEndDate'] ?? '',
                    "bzStartDate" => $post['forceBeginDate'] ?? '',
                    "bzEndDate" => $post['forceEndDate'] ?? '',
                    "bzInsureFlag" => $post['forceFlag'] ?? '1',
                    "busiInsureFlag" => $post['bizFlag'] ?? '1',
                    "bzVerifyCode" => $post['checkCodeCI'] ?? '',
                    "busiVerifyCode" => $post['checkCode'] ?? '',
                    "carShipFlag" => $post['carShipFlag'] ?? '1',
                ]
            ]
        ];
        $postData['body']['itemKindInfo'] = $this->setItem($post['optionInfo']);
        $postJson = json_encode($postData, JSON_UNESCAPED_UNICODE);
        $this->logger->freeQuote()->info("保司请求报文:" . $postJson);
        $header = ['Content-Type: application/json'];
        $url = $this->setUrl('100072');
        try {
            $result = $this->curl_https($url, $postJson, $header, __FUNCTION__,'120');
        } catch (\Exception $e) {
            return $this->withError($e->getMessage());
        }
        $this->logger->freeQuote()->info("保司响应报文:" . $result);
        $resultObj = json_decode($result,true);
        return $this->returnRes($resultObj);
    }

    private function setItem($items){
        $data = [];
        foreach ($items as $key => $item){
            $data[$key] = [
                'kindCode' => $item['kindCode'],
                'noDeductKindCode' => $item['noDeductKindCode'] ?? '',
                'kindName' => $item['kindName'] ?? '',
                'unitAmount' => $item['unitAmount'] ?? '',
                'quantity' => $item['quantity'] ?? '',
                'amount' => $item['kindValue'],
                'premium' => $item['kindPremium'] ?? '',
                'insureFlag' => 1,
            ];
        }
        return $data;
    }
}