<?php
/**
 * Created by PhpStorm.
 * User: luzhouyu
 * Date: 18/8/20
 * Time: 下午3:27
 */

namespace Uniondrug\PolicySdk\Sdks\TaiBaoZeRen\Modules;

trait Surrender
{
    public function surrender(array $post)
    {
        $xml_content = '<?xml version="1.0" encoding="UTF-8"?>
        <request>
            <head>
                <partnerCode>SZTC</partnerCode>
                <transactionCode>107004</transactionCode>
                <messageId>' . $post['waterNo'] . '</messageId>
                <transactionEffectiveDate>' . date("Y-m-d H:i:s") . '</transactionEffectiveDate>
                <user>' . $this->config->user . '</user>
                <password>' . $this->config->password . '</password>
            </head>
            <body>
                <entity>
                    <plcTerminalNo>3010100</plcTerminalNo>
                    <plcBusinessNo>' . $post['extend']['orderNo'] . '</plcBusinessNo>
                    <plcNo>' . $post['policyNo'] . '</plcNo>
                    <plcApplyReason>无法采集</plcApplyReason>
                </entity>
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
        if ($messageStatusCode != "000000") {
            $msg = $resultObj->head->responseCompleteMessageStatus->messageStatusDescriptionList->messageStatusDescription->messageStatusSubDescription;
            return $this->withError($msg);
        }
        if ($messageStatusCode == "000000") {
            $data = [
                'policyNo' => $post['policyNo'],
                'transTime' => date("Y-m-d H:i:s")
            ];
            return $this->withData($data);
        }
    }
}