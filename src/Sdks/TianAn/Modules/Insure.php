<?php

namespace Uniondrug\PolicySdk\Sdks\TianAn\Modules;

trait Insure
{
    public function insure(array $post,&$extResponse = [])
    {
        $postData = [
            'waterNo' => $post['waterNo'],
            'rationType' => $post['rationType'],
            'riskCode' => $post['riskCode'],
            'totalPremium' => $post['totalPremium'],
            'startDate' => $post['startDate'],
            'endDate' => $post['endDate'],
            'inputDate' => $post['inputDate'],
            'issuedDate' => $post['issuedDate'],
            'holderNum' => count($post['insuredList']),
            'appDataDto' => $this->getPolicyInfo($post['policyInfo'], $post['policyExt']),
            'insuredDataDtoList' => $this->getInsuredList($post['insuredList'], $post['insuredExt']),
            'dynamicDto' => $this->getDynamic($post['dynamicDto'], $post['dynamicExt'])
        ];
        if ($this->config->cooperation && $this->config->token) {
            $postData['requestHead'] = $this->createRequestHead($this->config->cooperation,$this->config->token);
        }
        $postJson = json_encode($postData,JSON_UNESCAPED_UNICODE);
        $this->logger->insure()->info("保司请求报文:".$postJson);
        $header = ['Content-Type: application/json'];
        try{
            $result = $this->curl_https($this->config->insure,$postJson,$header,__FUNCTION__);
        }catch (\Exception $e) {
            return $this->withError($e->getMessage());
        }
        $this->logger->insure()->info("保司响应报文:".$result);
        $resultObj = json_decode($result);
        if ($resultObj->retCode != "00") {
            $msg = (!$resultObj->retCode) ? $resultObj->resultDTO->resultMess : $resultObj->returnMsg;
            return $this->withError($msg);
        }
        $data = [
            'policyNo' => $resultObj->policyNo,
            'epolicyAddress' => urlencode(urldecode($resultObj->epolicyAddress)) ?: urlencode("http://www.tianan-life.com"),
            'transTime' => $resultObj->transTime ?: date("Y-m-d H:i:s"),
        ];
        return $this->withData($data);
    }

    protected function getPolicyInfo($policyInfo,$extSchema = [])
    {
        return [
            'applicantName' => $policyInfo['policyName'],
            'identifyType' => $policyInfo['policyIdentifyType'],
            'identifyNumber' => strtoupper($policyInfo['policyIdentifyNumber']),
            'mobile' => $policyInfo['policyMobile'],
            'birthday' => date("Y-m-d",strtotime($policyInfo['policyBirthday'])),
            'sex' => $policyInfo['policySex'],
            'applicantType' => '1'
        ];
    }

    protected function getInsuredList($insuredList, $extSchema = [])
    {
        $data = [];
        foreach ($insuredList as $value) {
            $data[] = [
                'insuredName' => $value['insuredName'],
                'identifyType' => $value['insuredIdentifyType'],
                'identifyNumber' => strtoupper($value['insuredIdentifyNumber']),
                'mobile' => $value['insuredMobile'],
                'birthday' => date("Y-m-d",strtotime($value['insuredBirthday'])),
                'sex' => $value['insuredSex'],
                'insRelationApp' => $value['insuredRelation'] ?: "99"
            ];
        }
        return $data;
    }

    public function getDynamic($dynamicDto, $dynamicExt = [])
    {
        if (in_array('ticketNo',$dynamicExt)) {
            $data['fieldAA'] = $dynamicDto['ticketNo'];
        }
        if (in_array('trafficStartTime',$dynamicExt)) {
            $data['fieldAB'] = $dynamicDto['trafficStartTime'];
        }
        if (in_array('departure',$dynamicExt)) {
            $data['fieldAD'] = $dynamicDto['departure'];
        }
        if (in_array('destination',$dynamicExt)) {
            $data['fieldAE'] = $dynamicDto['destination'];
        }
        $data = array_merge($data,[
            'fieldAF' => '无法采集',
            'fieldAG' => '无法采集',
            'fieldAH' => '无法采集',
            'fieldAI' => '无法采集',
            'fieldAJ' => '无法采集'
        ]);
        return $data;
    }

}