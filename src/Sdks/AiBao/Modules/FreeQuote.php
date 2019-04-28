<?php

namespace Uniondrug\PolicySdk\Sdks\AiBao\Modules;

trait FreeQuote
{
    public function freeQuote(array $post)
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
                "mainInfo" => [
                    "busiStartDate" => $post['busiStartDate'] ?? '',
                    "busiEndDate" => $post['busiEndDate'] ?? '',
                    "bzStartDate" => $post['bzStartDate'] ?? '',
                    "bzEndDate" => $post['bzEndDate'] ?? '',
                    "bzInsureFlag" => $post['bzInsureFlag'] ?? '1',
                    "busiInsureFlag" => $post['busiInsureFlag'] ?? '1',
                    "bzVerifyCode" => $post['bzVerifyCode'] ?? '',
                    "busiVerifyCode" => $post['busiVerifyCode'] ?? '',
                    "carShipFlag" => $post['carShipFlag'] ?? '1',
                ]
            ]
        ];
        $postData['body']['itemKindInfo'] = $this->setItem($post['items']);
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
        return $resultObj;
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
                'amount' => $item['amount'],
                'premium' => $item['premium'] ?? '',
                'insureFlag' => $item['insureFlag'],
            ];
        }
        return $data;
    }
}