<?php

namespace Uniondrug\PolicySdk\Sdks\GuoShou\Modules;

trait Insure
{
    public function insure(array $post, &$extResponse = [])
    {
        $postData = [
            'requestType' => "01",
            'projectCode' => $this->config->projectCode,
            'productCode' => $this->config->productCode,
            'programCode' => "PROG0264-DUIJIE-001",
            'mainEhm' => [
                'applyNum' => "1",
                'sendTime' => $post['inputDate'],
                'riskCode' => $post['riskCode'],
                'totalAmount' => $post['sumAssured'],
                'totalPremium' => $post['totalPremium'],
                'startDate' => $post['startDate'],
                'endDate' => $post['endDate'],
                'channelCode' => "09",
                'businessNature' => "0",
                'lifeOperatorCode' => "3199995103",
                'makeCom' => "31000033",
                'comCode' => "3100899003",
                'handler1Code' => "32032219771016401X",
                'approverCode' => "32032219771016401X",
                'argueSolution' => "1",
                'engageFlag' => "Y",
                'jfeeFlag' => "0"
            ],
            'applicantEhm' => $this->getPolicyInfo($post['policyInfo'], $post['policyExt']),
            'insuredEhmArray' => $this->getInsuredList($post['insuredList'], $post['insuredExt']),
        ];
        $postJson = json_encode($postData, JSON_UNESCAPED_UNICODE);
        $this->logger->insure()->info("保司请求报文:" . $postJson);
        $header = ['Content-Type: application/json'];
        try {
            $result = $this->curl_https($this->config->insure, $postJson, $header, __FUNCTION__, 90);
        } catch (\Exception $e) {
            return $this->withError($e->getMessage());
        }
        $this->logger->insure()->info("保司响应报文:" . $result);
        $resultObj = json_decode($result);
        if ($resultObj->responseCode != 0) {
            return $this->withError($resultObj->errorMessage);
        }
        $data = [
            'policyNo' => $resultObj->proposalNo,
            'epolicyAddress' => "",
            'transTime' => date("Y-m-d H:i:s"),
        ];
        return $this->withData($data);
    }

    protected function getPolicyInfo($policyInfo, $extSchema = [])
    {
        return [
            'appliName' => $policyInfo['policyName'],
            'insuredNature' => "3",
            'induredIdentity' => "01",
            'identifyNumber' => $policyInfo['policyIdentifyNumber'],
            'identifyType' => $this->convertIdentifyType($policyInfo['policyIdentifyType']),
            'mobile' => $policyInfo['policyMobile']
        ];
    }

    protected function getInsuredList($insuredList, $extSchema = [])
    {
        $data = [];
        foreach ($insuredList as $value) {
            $data[] = [
                'insuredName' => $value['insuredName'],
                'insuredNature' => "3",
                'identifyNumber' => $value['insuredIdentifyNumber'],
                'identifyType' => $this->convertIdentifyType($value['insuredIdentifyType']),
                'valid' => "true"
            ];
        }
        return $data;
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
                $type = "01";
                break;
            //  军官证
            case "02":
                $type = "04";
                break;
            //  护照
            case "03":
                $type = "03";
                break;
            //  港澳通行证
            case "04":
                $type = "14";
                break;
            //  台湾通行证
            case "05":
                $type = "15";
                break;
            //  驾驶证
            case "06";
                $type = "05";
                break;
            //  出生证
            case "07";
                $type = "02";
                break;
            //  外国人居住证
            case "08":
                $type = "10";
                break;
            //  组织机构代码
            case "09":
                $type = "07";
                break;
            default:
                $type = "99";
                break;
        }
        return $type;
    }
}