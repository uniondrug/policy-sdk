<?php

namespace Uniondrug\PolicySdk\Sdks\HuaXia\Modules;

trait Insure
{
    public function insure(array $post, &$extResponse = [])
    {
        $postData = [
            'cooperation' => 'tongcheng',
            'waterNo' => $post['waterNo'],
            'orderNo' => $post['orderNo'],
            'rationType' => $post['rationType'],
            'startDate' => $post['startDate'],
            'endDate' => $post['endDate'],
        ];
        $this->getPolicyInfo($post['policyInfo'], $post['policyExt'], $postData);
        $this->getInsuredList($post['insuredList'], $post['insuredExt'], $postData);
        $this->getDynamic($post['dynamicDto'], $post['dynamicExt'], $postData);
        /*
         * 组装报文
         */
        $postData = $this->createParams($postData);
        $this->logger->insure()->info("保司请求报文:" . json_encode($postData, JSON_UNESCAPED_UNICODE));
        $header = ['Content-Type: application/x-www-form-urlencoded'];
        $postQuery = http_build_query($postData);
        try {
            $result = $this->curl_https($this->config->insure, $postQuery, $header, __FUNCTION__);
        } catch (\Exception $e) {
            return $this->withError($e->getMessage());
        }
        $this->logger->insure()->info("保司响应报文:" . $result);
        $resultObj = json_decode($result);
        if ($resultObj->retCode != "00") {
            return $this->withError($resultObj->retDesc);
        }
        $data = [
            'policyNo' => $resultObj->policyNum,
            'epolicyAddress' => urlencode(urldecode($resultObj->edocPath)),
            'transTime' => $resultObj->transTime ?: date("Y-m-d H:i:s"),
        ];
        return $this->withData($data);
    }

    protected function getPolicyInfo($policyInfo, $extSchema = [], &$postData = [])
    {
        $data = [
            'policyName' => $policyInfo['policyName'],
            'policySex' => $policyInfo['policySex'] ?: "",
            'policyIdentifyType' => $policyInfo['policyIdentifyType'] ?: "",
            'policyIdentifyNumber' => $policyInfo['policyIdentifyNumber'] ?: "",
            'policyBirthday' => date("Ymd", strtotime($policyInfo['policyBirthday'])) ?: "",
            'policyMobile' => $policyInfo['policyMobile'] ?: "",
            'policyAddress' => $policyInfo['policyAddress'] ?: "",
            'policyEmail' => $policyInfo['policyEmail'] ?: "",
            'policyZipCode' => $policyInfo['policyZipCode'] ?: "",
        ];
        $postData = array_merge($postData, $data);
    }

    protected function getInsuredList($insuredList, $extSchema = [], &$postData = [])
    {
        $data = [];
        foreach ($insuredList as $value) {
            $data[] = [
                'insuredName' => $value['insuredName'] ?: "",
                'insuredSex' => $value['insuredSex'] ?: "",
                'insuredIdentifyType' => $value['insuredIdentifyType'] ?: "",
                'insuredIdentifyNumber' => $value['insuredIdentifyNumber'] ?: "",
                'insuredBirthday' => date("Ymd", strtotime($value['insuredBirthday'])) ?: "",
                'insuredMobile' => $value['insuredMobile'] ?: "",
                'insuredAddress' => $value['insuredAddress'] ?: "",
                'insuredPolRelation' => $value['insuredRelation'] ?: "",
                'insuredBenRelation' => $value['insuredRelation'] ?: "",
                'insuredEmail' => $value['insuredEmail'] ?: "",
                'insuredZipCode' => $value['insuredZipCode'] ?: "",
            ];
        }
        $postData['insuredList'] = $data;
    }

    public function getDynamic($dynamicDto, $dynamicExt = [], &$postData = [])
    {
        if (in_array('ticketNo', $dynamicExt)) {
            $data['FlightNo'] = $dynamicDto['ticketNo'] ?: "";
        }
        if (in_array('trafficStartTime', $dynamicExt)) {
            $data['date'] = date("Ymd", strtotime($dynamicDto['trafficStartTime'])) ?: "";
        }
        $postData = array_merge($postData, $data);
    }
}