<?php

namespace Uniondrug\PolicySdk\Sdks\TaiBaoZeRen\Modules;

trait Insure
{
    public function insure(array $post, &$extResponse = [])
    {
        $xml_content = '<?xml version="1.0" encoding="UTF-8"?>
        <request>
            <head>
                <partnerCode>SZTC</partnerCode>
                <transactionCode>107001</transactionCode>
                <messageId>' . $post['waterNo'] . '</messageId>
                <transactionEffectiveDate>' . date("Y-m-d H:i:s") . '</transactionEffectiveDate>
                <user>' . $this->config->user . '</user>
                <password>' . $this->config->password . '</password>
            </head>
            <body>
                 <entity>
                    <plcBase>
                        <plcTerminalNo>3010100</plcTerminalNo>
                        <plcBusinessNo>' . $post['orderNo'] . '</plcBusinessNo>
                        <plcPlanCode>' . $post['riskCode'] . '</plcPlanCode>
                        <plcStartDate>' . date("YmdH", strtotime($post['startDate'])) . '</plcStartDate>
                        <plcEndDate>' . date("YmdH", strtotime($post['endDate'])) . '</plcEndDate>
                        <plcPremium>' . $post['totalPremium'] . '</plcPremium>
                        <plcAmount>' . $post['sumAssured'] . '</plcAmount>
                        <plcCopies>1</plcCopies>
                        <plcElcFlag>0</plcElcFlag>
                    </plcBase>
                    ' . $this->getPolicyInfo($post['policyInfo'], $post['policyExt']) . '
                    ' . $this->getInsuredList($post['insuredList'], $post['insuredExt']) . '
                    ' . $this->getDynamic($post['dynamicDto'], $post['dynamicExt'], $post) . '
                    <elcPolicy>
                        <elcMsgFlag>0</elcMsgFlag>
                        <elcMobile/>
                        <elcEmlFlag>0</elcEmlFlag>
                        <elcEmail/>
                    </elcPolicy>
                </entity>
            </body>
        </request>';
        $postData = array(
            'requestMessage' => $xml_content,
            'documentProtocol' => 'CPIC_ECOM',
            'messageRouter' => '3',
            'tradingPartner' => 'SZTC',
        );
        $this->logger->insure()->info("保司请求报文:" . $xml_content);
        $header = ['Content-Type: application/x-www-form-urlencoded'];;
        $postQuery = http_build_query($postData);
        try {
            $result = $this->curl_https($this->config->insure, $postQuery, $header, __FUNCTION__);
        } catch (\Exception $e) {
            return $this->withError($e->getMessage());
        }
        $this->logger->insure()->info("保司响应报文:" . $result);
        $resultObj = xml_to_object($result);
        $messageStatusCode = $resultObj->head->responseCompleteMessageStatus->messageStatusCode;
        if ($messageStatusCode != "000000") {
            $msg = $resultObj->head->responseCompleteMessageStatus->messageStatusDescriptionList->messageStatusDescription->messageStatusSubDescription;
            return $this->withError($msg);
        }
        $dataObj = $resultObj->body->entity->plcBase;
        $data = [
            'policyNo' => $dataObj->plcNo,
            'epolicyAddress' => urlencode($dataObj->epolicyInfo),
            'transTime' => $dataObj->effectiveDate ?: date("Y-m-d H:i:s"),
        ];
        $extResponse = [
            'orderNo' => $post['orderNo']
        ];
        return $this->withData($data);
    }

    public function getDynamic($dynamicDto, $dynamicExt = [], $post = [])
    {
        $data = '<ppublicaddress>';
        if (in_array('ticketNo', $dynamicExt)) {
            $data .= '<bookDate>' . date("Ymd", strtotime($post['inputDate'])) . '</bookDate>';
            $data .= '<flightNo>' . $dynamicDto['ticketNo'] . '</flightNo>';
        }
        if (in_array('departure', $dynamicExt)) {
            $data .= '<departureCity>' . $dynamicDto['departure'] . '</departureCity>';
        }
        if (in_array('destination', $dynamicExt)) {
            $data .= '<arrivalCity>' . $dynamicDto['destination'] . '</arrivalCity>';
        }
        if (in_array('trafficStartTime', $dynamicExt)) {
            $data .= '<departureDate>' . date("Ymd", strtotime($dynamicDto['trafficStartTime'])) . '</departureDate>';
            $data .= '<departureTime>' . date("YmdHis", strtotime($dynamicDto['trafficStartTime'])) . '</departureTime>';
        }
        if (in_array('trafficEndTime', $dynamicExt)) {
            $data .= '<arrivalDateTime>' . date("YmdHis", strtotime($dynamicDto['trafficEndTime'])) . '</arrivalDateTime>';
        }
        $data .= '</ppublicaddress>';
        return $data;
    }

    protected function getInsuredList($insuredList, $extSchema = [])
    {
        $data = '<insuredList>';
        foreach ($insuredList as $key => $value) {
            $data .= '<insured>
                        <isrdName>' . $value['insuredName'] . '</isrdName>
                        <isrdCretType>' . $this->convertIdentifyType($value['insuredIdentifyType']) . '</isrdCretType>
                        <isrdCretCode>' . $value['insuredIdentifyNumber'] . '</isrdCretCode>
                        <isrdTelephone/>
                        <isrdEmail/>
                        <isrdMobile>' . $value['insuredMobile'] . '</isrdMobile>
                        <isrdAddress/>
                    </insured>';
        }
        $data .= '</insuredList>';
        return $data;
    }

    protected function getPolicyInfo($policyInfo, $extSchema = [])
    {
        return '<applicant>
                    <apltName>' . $policyInfo['policyName'] . '</apltName>
                    <apltCretType>' . $this->convertIdentifyType($policyInfo['policyIdentifyType']) . '</apltCretType>
                    <apltCretCode>' . $policyInfo['policyIdentifyNumber'] . '</apltCretCode>
                    <apltTelephone/>
                    <apltEmail/>
                    <apltMobile>' . $policyInfo['policyMobile'] . '</apltMobile>
                    <apltAddress/>
                </applicant>';
    }

    /**
     * 证件类型转换
     * @param $identityType
     * @return int
     */
    protected function convertIdentifyType($identityType)
    {
        switch ($identityType) {
            //  身份证
            case "01":
                $type = 1;
                break;
            //  护照
            case "03":
                $type = 2;
                break;
            //  组织机构代码
            case "09":
                $type = 4;
                break;
            //  营业证号
            case "10":
                $type = 5;
                break;
            //  其它
            default:
                $type = 3;
                break;
        }
        return $type;
    }
}