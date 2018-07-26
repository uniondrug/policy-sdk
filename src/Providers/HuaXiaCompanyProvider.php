<?php
/**
 * Created by PhpStorm.
 * User: luzhouyu
 * Date: 18/7/25
 * Time: 下午11:31
 */

namespace Uniondrug\PolicySdk\Providers;

/**
 * 华夏保司
 * Class HuaXiaCompanyProvider
 * @package Uniondrug\PolicyService\Providers
 */
class HuaXiaCompanyProvider extends AbstractCompanyProvider
{
    public function insure(array $post, &$extResponse = [])
    {
        $this->logger->insure()->info('asa');die;
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
            $result = $this->curl_https($this->config->insure, $postQuery, $header, 'insure');
        } catch (\Exception $e) {
            return $this->apiResponse->withError($e->getMessage());
        }
        $this->logger->insure()->info("保司响应报文:" . $result);
        $resultObj = json_decode($result);
        if ($resultObj->retCode != "00") {
            return $this->apiResponse->withError($resultObj->retDesc);
        }
        $data = [
            'policyNo' => $resultObj->policyNum,
            'epolicyAddress' => urlencode(urldecode($resultObj->edocPath)),
            'transTime' => $resultObj->transTime ?: date("Y-m-d H:i:s"),
        ];
        return $this->apiResponse->withData($data);
    }

    public function surrender(array $post)
    {
        $postData = [
            'cooperation' => 'tongcheng',
            'waterNo' => $post['waterNo'],
            'policyNo' => $post['policyNo'],
            'productCode' => $post['rationType'],
            'cancelReason' => '接口保单注销'
        ];
        /*
        * 组装报文
        */
        $postData = $this->createParams($postData);
        $this->logger->surrender()->info("保司请求报文:" . json_encode($postData, JSON_UNESCAPED_UNICODE));
        $header = ['Content-Type: application/x-www-form-urlencoded'];
        $postQuery = http_build_query($postData);
        try {
            $result = $this->curl_https($this->config->surrender, $postQuery, $header, 'surrender');
        } catch (\Exception $e) {
            return $this->apiResponse->withError($e->getMessage());
        }
        $this->logger->surrender()->info("保司响应报文:" . $result);
        $resultObj = json_decode($result);
        if ($resultObj->retCode != "00") {
            $msg = $resultObj->retDesc ?: $resultObj->returnMsg;
            return $this->apiResponse->withError($msg);
        }
        $data = [
            'policyNo' => $post['policyNo'],
            'transTime' => $resultObj->transTime ?: date("Y-m-d H:i:s")
        ];
        return $this->apiResponse->withData($data);
    }


    private function createParams($postData)
    {
        $params = [
            'uid' => 'adpt_tongcheng',
            'timestamp' => str_replace(".", "", microtime(1)),
            'nonce' => substr(md5(microtime(1)), 0, 20),
            'data' => json_encode($postData, JSON_UNESCAPED_UNICODE)
        ];
        $params['signature'] = md5($this->getLinkString($params) . $this->localConfig->key);
        return $params;
    }

    private function getLinkString($params)
    {
        $params = array_filter($params); // 过滤空值
        unset($params['signature']); // 去掉签名,如有
        ksort($params);
        $peers = array();
        foreach ($params as $k => $v) {
            $peers[] = "$k=$v";
        }
        return implode("&", $peers);
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
            $data['date'] = $dynamicDto['trafficStartTime'] ?: "";
        }
        $postData = array_merge($postData, $data);
    }
}