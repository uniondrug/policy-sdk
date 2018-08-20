<?php

namespace Uniondrug\PolicySdk\Sdks\TaiBaoYiWai\Modules;

trait Surrender
{
    public function surrender(array $post)
    {
        $xml_content = '<?xml version="1.0" encoding="UTF-8"?>
        <request>
            <head>
                <partnerCode>SZTC</partnerCode>
                <transactionCode>108003</transactionCode>
                <messageId>' . $post['extend']['billNo'] . '</messageId>
                <transactionEffectiveDate>' . date("Y-m-d H:i:s") . '</transactionEffectiveDate>
                <user>' . $this->config->user . '</user>
                <password>' . $this->config->password . '</password>
            </head>
            <body>
                <PolicyCancellationRequest>
                    <terminalNo>3010100</terminalNo>
                    <PolicyCancellationBaseInfo>
                        <policyNo>' . $post['policyNo'] . '</policyNo>
                        <applicationReason>退保</applicationReason>
                        <billType>0</billType>
                        <billNo>' . $post['extend']['billNo'] . '</billNo>
                        <contentType>1</contentType>
                        <wifiFlag>1</wifiFlag>
                    </PolicyCancellationBaseInfo>
                     <Proposer>
                        <customerName>N/A</customerName>
                        <certificateType>3</certificateType>
                        <certificateCode>N/A</certificateCode>
                        <accountName/>
                        <accountBank/>
                        <account/>
                    </Proposer>
                    <EPolicyInfo>
                        <messageFlag>0</messageFlag>
                        <electronPolicyMobile/>
                        <emailFlag>0</emailFlag>
                        <electronPolicyEmail/>
                    </EPolicyInfo>
                </PolicyCancellationRequest>
            </body>
        </request>';
        $postData = array(
            'requestMessage' => $xml_content,
            'documentProtocol' => 'CPIC_ECOM',
            'messageRouter' => '3',
            'tradingPartner' => 'SZTC',
        );
        $this->logger->surrender()->info("保司请求报文:" . $xml_content);
        $header = ['Content-Type: application/x-www-form-urlencoded'];
        $postQuery = http_build_query($postData);
        try {
            $result = $this->curl_https($this->config->surrender, $postQuery, $header, __FUNCTION__);
        } catch (\Exception $e) {
            return $this->withError($e->getMessage());
        }
        $this->logger->surrender()->info("保司响应报文:" . $result);
        $resultObj = xml_to_object($result);
        $messageStatusCode = $resultObj->head->responseCompleteMessageStatus->messageStatusCode;
        $resultCode = $resultObj->body->PolicyCancellationResponse->resultCode;
        if ($messageStatusCode == "000000" && (!$resultCode || in_array($resultCode, ["00", "02"]))) {
            $data = [
                'policyNo' => $post['policyNo'],
                'transTime' => $resultObj->transTime ?: date("Y-m-d H:i:s")
            ];
            return $this->withData($data);
        } else {
            switch ($resultCode) {
                case "01":
                    $msg = "保单不存在";
                    break;
                case "03":
                    $msg = "保单状态不是已生效不能做退保";
                    break;
                case "04":
                    $msg = "保单已起保不能做退保";
                    break;
                case "05":
                    $msg = "保单批改中不能做退保";
                    break;
                default:
                    $msg = $resultObj->head->responseCompleteMessageStatus->messageStatusDescriptionList->messageStatusDescription->messageStatusSubDescription;
                    break;
            }
            return $this->withError($msg);
        }
    }
}