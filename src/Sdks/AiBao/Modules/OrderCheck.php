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
                    "birthday" => $post['insurantBirthday'],
                    "idNo" => $post['insurantIdNo'],
                    "idType" => $this->convertIdentifyType($post['insurantIdType']),
                    "idName" => $post['insurantIdName'],
                    "sex" => $post['insurantSex'],
                    "mobile" => $post['insurantMobile'],
                    "mobileHolederName" => $post['mobileHolederName'],
                    "mobileHolederIdType" => $this->convertIdentifyType($post['mobileHolederIdType']) ,
                    "mobileHolederIdNo" => $post['mobileHolederIdNo'],
                    "email" => $post['email'] ?? '',
                    "address" => $post['address'] ?? '',
                ],
                "destinationInfo" => [
                    "idName" => $post['receiveIdName'],
                    "mobile" => $post['receiveMobile'],
                    "email" => $post['email'] ?? '',
                    "sendDate" => $post['sendDate'] ?? '',
                    "invoice" => $post['invoice'],
                    "invoiceFlag" => $post['invoiceFlag'] ?? '',
                    "policyFlagBI" => $post['policyFlagBI'] ?? '',
                    "policyFlagCI" => $post['policyFlagCI'] ?? '',
                    "province" => $post['province'],
                    "city" => $post['city'],
                    "town" => $post['town'],
                    "address" => $post['address'],
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
        return $resultObj;
/*        if ($resultObj->head->errorCode != "0000") {
            return $this->withError($resultObj->head->errorMsg);
        }
        $data = [
            'aiBaoTransactionNo' => $resultObj->head->aiBaoTransactionNo,
            'transactionNo' => $resultObj->head->transactionNo,
            'orderNo' => $resultObj->body->mainInfo->orderNo,
            'busiProposalNo' => $resultObj->body->mainInfo->busiProposalNo,
            'bzProposalNo' => $resultObj->body->mainInfo->bzProposalNo,
            'prePayOrderCreateTime' => $resultObj->body->mainInfo->prePayOrderCreateTime,
            'effectivePaymentTime' => $resultObj->body->mainInfo->effectivePaymentTime,
            'bzVerifyCodeImg' => $resultObj->body->mainInfo->bzVerifyCodeImg,
            'busiVerifyCodeImg' => $resultObj->body->mainInfo->busiVerifyCodeImg,
        ];
        return $this->withData($data);*/
    }
}