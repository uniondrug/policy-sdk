<?php

namespace Uniondrug\PolicySdk\Sdks\AiBao\Modules;

trait OrderCheck
{
    public function orderCheck(array $post)
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
                    "returnUrl" => $post['returnUrl'],
                    "notifyUrl" => $post['notifyUrl'],
                    "verifyCode" => $post['verifyCode'] ?? '',
                    "bzVerifyCode" => $post['bzVerifyCode'] ?? '',
                    "busiVerifyCode" => $post['busiVerifyCode'] ?? ''
                ],
                "applicantInfo" => [
                    "birthday" => $post['applicantBirthday'],
                    "idNo" => $post['applicantIdNo'],
                    "idType" => $this->convertIdentifyType($post['applicantIdType']),
                    "idName" => $post['applicantName'],
                    "sex" => $post['applicantSex'],
                    "mobile" => $post['applicantMobile'],
                    "mobileHolederName" => $post['mobileHolederName'],
                    "mobileHolederIdType" => $this->convertIdentifyType($post['mobileHolederIdType']) ,
                    "mobileHolederIdNo" => $post['mobileHolederIdNo'],
                    "email" => $post['applicantEmail'] ?? '',
                    "address" => $post['applicantAddress'] ?? '',
                ],
                "destinationInfo" => [
                    "idName" => $post['receiveName'],
                    "mobile" => $post['receiveMobile'],
                    "email" => $post['receiveEmail'] ?? '',
                    "sendDate" => $post['sendDate'] ?? '',
                    "invoice" => $post['invoice'],
                    "invoiceFlag" => $post['invoiceFlag'] ?? '',
                    "policyFlagBI" => $post['policyFlagBI'] ?? '',
                    "policyFlagCI" => $post['policyFlagCI'] ?? '',
                    "province" => $post['provinceCode'],
                    "city" => $post['cityCode'],
                    "town" => $post['townCode'],
                    "address" => $post['receiveAddress'],
                    "readingCompletedTime" => $post['readingCompletedTime'] ?? '',
                    "agreeAuthorizeTime" => $post['agreeAuthorizeTime'] ?? '',
                    "checkedTime" => $post['checkedTime'] ?? '',
                ]
            ]
        ];
        $postJson = json_encode($postData, JSON_UNESCAPED_UNICODE);
        $this->logger->orderCheck()->info("保司请求报文:" . $postJson);
        $header = ['Content-Type: application/json'];
        $url = $this->setUrl('100073');
        try {
            $result = $this->curl_https($url, $postJson, $header, __FUNCTION__,'120');
        } catch (\Exception $e) {
            return $this->withError($e->getMessage());
        }
        $this->logger->orderCheck()->info("保司响应报文:" . $result);
        $resultObj = json_decode($result,true);
        return $this->returnRes($resultObj);
    }
}