<?php

namespace Uniondrug\PolicySdk\Sdks\YongAn\Modules;

trait Insure
{
    public function insure(array $post, &$extResponse = [])
    {
        $waterNo = $this->createUniqueWaterNo();
        $date = date('Y-m-d\TH:i:s+08:00');
        $postData = ['arg0' => [
            "baseInfo" => [
                "user" => "TCLY",
                "password" => "1234567890",
                "ccardbsnstyp" => "TIT_TCLY",
                "opercode" => "310084",
                "isgrp" => "0",
                // 以上为固定字段
                "serialno" => $waterNo,
                "rationcode" => $post['riskCode'],
                "amt" => $post['totalPremium'],
                "tapptm" => $date,
                "tissuetm" => $date,
                "tinsrncbgntm" => $this->ISODateString($post['startDate']),
                "tinsrncendtm" => $this->ISODateString($post['endDate']),
            ],
            "appInfos" => [
                "appBaseInfo" => $this->getPolicyInfo($post['policyInfo'], $post['policyExt']),
                // 被保人
                "insuredInfos" => $this->getInsuredList($post['insuredList'], $post['insuredExt'], $post['policyInfo']['policyMobile']),
                //航班信息
                "propertyInfos" => $this->getDynamic($post['dynamicDto'], $post['dynamicExt'])
            ]
        ]];
        $url = $this->config->insure;
        $client = new \nusoap_client($url, 'wsdl');
        $client->soap_defencoding = 'utf-8';
        $client->decode_utf8 = false;
        $client->xml_encoding = 'utf-8';
        $this->logger->insure()->info("保司请求报文:" . json_encode($postData, JSON_UNESCAPED_UNICODE));
        $resultObj = $client->call('appRequest', $postData);
        $this->logger->insure()->info("保司响应报文:" . json_encode($resultObj, JSON_UNESCAPED_UNICODE));
        if ($err = $client->getError()) {
            return $this->withError($err);
        }
        $returnObj = $resultObj['return'];
        /*
         * 投保失败
         */
        if ($returnObj['flag']) {
            return $this->withError($returnObj['reason']);
        }
        $data = [
            'policyNo' => $returnObj['appInfoRes']['policyno'],
            'epolicyAddress' => urlencode(urldecode($returnObj['appInfoRes']['pdfurl'])),
            'transTime' => $returnObj['appInfoRes']['transTime'] ?: date("Y-m-d H:i:s"),
        ];
        $extResponse = [
            'waterNo' => $waterNo
        ];
        return $this->withData($data);
    }

    protected function getPolicyInfo($policyInfo, $extSchema = [])
    {
        return [
            'businessid' => 1,
            'apptype' => $this->convertIdentifyType($policyInfo['policyIdentifyType']),
            'appname' => $policyInfo['policyName'],
            'appid' => $policyInfo['policyIdentifyNumber'],
            'appphone' => $policyInfo['policyMobile'] ?: "",
            'appbirthday' => date("Y-m-d", strtotime($policyInfo['policyBirthday'])),
        ];
    }

    protected function getInsuredList($insuredList, $extSchema = [], $policyMobile = "")
    {
        $data = [];
        foreach ($insuredList as $value) {
            $data[] = [
                'ptype' => $this->convertIdentifyType($value['insuredIdentifyType']),
                'apprel' => '601005',   //  本人
                'pname' => $value['insuredName'],
                'pid' => $value['insuredIdentifyNumber'],
                'pbirthday' => date("Y-m-d", strtotime($value['insuredBirthday'])),
                'psex' => $value['insuredSex'] == "01" ? 1 : 2,
                'ptel' => $value['insuredMobile'] ?: $policyMobile
            ];
        }
        return $data[0];
    }

    public function getDynamic($dynamicDto, $dynamicExt = [])
    {
        $data['seqno'] = 1;
        if (in_array('ticketNo', $dynamicExt)) {
            $data['flyno'] = $dynamicDto['ticketNo'];
        }
        if (in_array('trafficStartTime', $dynamicExt)) {
            $data['flydate'] = $this->ISODateString($dynamicDto['trafficStartTime']);
        }
        if (in_array('trafficEndTime', $dynamicExt)) {
            $data['flylanddate'] = $this->ISODateString($dynamicDto['trafficEndTime']);
        }
        if (in_array('departure', $dynamicExt)) {
            $data['startsitename'] = $dynamicDto['departure'];
        }
        if (in_array('destination', $dynamicExt)) {
            $data['endsitename'] = $dynamicDto['destination'];
        }
        return $data;
    }

    /**
     * 证件类型转换
     * @param $identityType
     * @return string
     */
    protected function convertIdentifyType($identityType)
    {
        switch ($identityType) {
            //  身份证
            case "01":
                $type = "120001";
                break;
            //  军官证
            case "02":
                $type = "120003";
                break;
            //  护照
            case "03":
                $type = "120002";
                break;
            //  营业证号
            case "10":
                $type = "110002";
                break;
            //  组织机构代码
            case "09":
                $type = "110001";
                break;
            //  其它
            default:
                $type = "120009";
                break;
        }
        return $type;
    }
}