<?php
/**
 * Created by PhpStorm.
 * User: luzhouyu
 * Date: 18/7/26
 * Time: 下午3:42
 */

namespace Uniondrug\PolicySdk\Modules;
use Uniondrug\PolicySdk\Sdk;

/**
 * 太保保司xml报文
 * Class TaibaoCompanySdk
 * @package Uniondrug\PolicySdk\Modules
 */
class TaibaoCompanySdk extends Sdk
{
    const sdkName = "TAIBAO";

    public function __construct()
    {
        parent::__construct(self::sdkName);
    }

    public function insure(array $post, &$extResponse = [])
    {
        $xml_content = '<?xml version="1.0" encoding="UTF-8"?>
        <request>
            <head>
                <partnerCode>SZTC</partnerCode>
                <transactionCode>108001</transactionCode>
                <messageId>' . $post['waterNo'] . '</messageId>
                <transactionEffectiveDate>' . date("Y-m-d H:i:s") . '</transactionEffectiveDate>
                <user>' . $this->config->user . '</user>
                <password>' . $this->config->password . '</password>
            </head>
            <body>
                <PolicyApplyRequest>
                    <PolicyBaseInfo>
                        <terminalNo>3010100</terminalNo>
                        <planCode>' . $post['riskCode'] . '</planCode>
                        <groupInsuranceFlag>S</groupInsuranceFlag>
                        <billType>0</billType>
                        <coverageCopies>1</coverageCopies>
                        <startDate>' . $post['startDate'] . '</startDate>
                        <endDate>' . $post['endDate'] . '</endDate>
                        <sumInsured/>
                        <policyPremium/>
                        <uniqueFlag>' . $post['waterNo'] . '</uniqueFlag>
                    </PolicyBaseInfo>
                   ' . $this->getPolicyInfo($post['policyInfo'], $post['policyExt']) . '
                   ' . $this->getInsuredList($post['insuredList'], $post['insuredExt']) . '
                   ' . $this->getDynamic($post['dynamicDto'], $post['dynamicExt'], $post) . '
                    <EPolicyInfo>
                        <messageFlag>0</messageFlag>
                        <electronPolicyMobile/>
                        <emailFlag>0</emailFlag>
                        <isEInvoiceFlag>1</isEInvoiceFlag>
                        <electronPolicyEmail/>
                        <returnPDFFlag/>
                    </EPolicyInfo>
                </PolicyApplyRequest>
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
            $result = $this->curl_https($this->config->insure, $postQuery, $header, 'insure');
        } catch (\Exception $e) {
            return $this->withError($e->getMessage());
        }
        $this->logger->insure()->info("保司响应报文:" . $result);
        $resultObj = json_decode(str_replace("{}", '""', json_encode((array)simplexml_load_string($result))));
        $messageStatusCode = $resultObj->head->responseCompleteMessageStatus->messageStatusCode;
        if ($messageStatusCode != "000000") {
            $msg = $resultObj->head->responseCompleteMessageStatus->messageStatusDescriptionList->messageStatusDescription->messageStatusSubDescription;
            return $this->withError($msg);
        }
        $dataObj = $resultObj->body->PolicyApplyResponse;
        $data = [
            'policyNo' => $dataObj->policyNo,
            'epolicyAddress' => urlencode(urldecode($dataObj->policyUrl)),
            'transTime' => $dataObj->effectiveDate ?: date("Y-m-d H:i:s"),
        ];
        $extResponse = [
            'billNo' => $dataObj->billNo
        ];
        return $this->withData($data);

    }


    public function surrender(array $post)
    {
        $xml_content = '<?xml version="1.0" encoding="UTF-8"?>
        <request>
            <head>
                <partnerCode>SZTC</partnerCode>
                <transactionCode>108003</transactionCode>
                <messageId>' . $post['billNo'] . '</messageId>
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
                        <billNo>' . $post['billNo'] . '</billNo>
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
            $result = $this->curl_https($this->config->surrender, $postQuery, $header, 'insure');
        } catch (\Exception $e) {
            return $this->withError($e->getMessage());
        }
        $this->logger->surrender()->info("保司响应报文:" . $result);
        $resultObj = json_decode(str_replace("{}", '""', json_encode((array)simplexml_load_string($result))));
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

    /*
      因子代码： 因子名称：
      HC000     订票日期
      HC001     航班目的地
      HC002     航班起飞时间
      HC003     航班到达时间
      B4001     航班号
      B4002     乘机日期
      W3000     起始地点
    */
    public function getDynamic($dynamicDto, $dynamicExt = [], $post = [])
    {
        $data = '<FactorList>';
        if (in_array('ticketNo', $dynamicExt)) {
            $data .= '<Factor>
                    <factorCode>HC000</factorCode>
                    <factorValue>' . $post['inputDate'] . '</factorValue>
                  </Factor>';
            $data .= '<Factor>
                        <factorCode>B4001</factorCode>
                        <factorValue>' . $dynamicDto['ticketNo'] . '</factorValue>
				      </Factor>';
        }
        if (in_array('trafficStartTime', $dynamicExt)) {
            $data .= '<Factor>
                        <factorCode>HC002</factorCode>
                        <factorValue>' . $dynamicDto['trafficStartTime'] . '</factorValue>
				      </Factor>';
        }
        if (in_array('trafficEndTime', $dynamicExt)) {
            $data .= '<Factor>
                        <factorCode>HC003</factorCode>
                        <factorValue>' . $dynamicDto['trafficEndTime'] . '</factorValue>
				      </Factor>';
        }
        if (in_array('departure', $dynamicExt)) {
            $data .= '<Factor>
                        <factorCode>W3000</factorCode>
                        <factorValue>' . $dynamicDto['departure'] . '</factorValue>
				      </Factor>';
        }
        if (in_array('destination', $dynamicExt)) {
            $data .= '<Factor>
                        <factorCode>HC001</factorCode>
                        <factorValue>' . $dynamicDto['destination'] . '</factorValue>
				      </Factor>';
        }
        $data .= '</FactorList>';
        return $data;
    }

    protected function getInsuredList($insuredList, $extSchema = [])
    {
        $data = '<InsuredList>';
        foreach ($insuredList as $key => $value) {
            $data .= '<Insured>
                        <insuredCode>' . ($key + 1) . '</insuredCode>
                        <customerName>' . $value['insuredName'] . '</customerName>
                        <customerNamePingYing/>
                        <certificateType>' . $this->convertIdentifyType($value['insuredIdentifyType']) . '</certificateType>
                        <certificateCode>' . $value['insuredIdentifyNumber'] . '</certificateCode>
                        <customerGender>' . $this->getSex($value['insuredSex'], $value['insuredIdentifyNumber']) . '</customerGender>
                        <customerBirthday>' . date("Y-m-d", strtotime($value['insuredBirthday'])) . '</customerBirthday>
                        <benefitWay>1</benefitWay>
                    </Insured>';
        }
        $data .= '</InsuredList>';
        return $data;
    }

    protected function getPolicyInfo($policyInfo, $extSchema = [])
    {
        return '<Applicant>
                    <customerName>' . $policyInfo['policyName'] . '</customerName>
                    <certificateType>' . $this->convertIdentifyType($policyInfo['policyIdentifyType']) . '</certificateType>
                    <certificateCode>' . $policyInfo['policyIdentifyNumber'] . '</certificateCode>
                    <customerGender>' . $this->getSex($policyInfo['policySex'], $policyInfo['policyIdentifyNumber']) . '</customerGender>
                    <customerBirthday>' . date("Y-m-d", strtotime($policyInfo['policyBirthday'])) . '</customerBirthday>
                    <comAddress/>
                    <mobile>' . $policyInfo['policyMobile'] . '</mobile>
                    <email/>
                    <customerIndustryType>O</customerIndustryType>
                    <areaCode>310109</areaCode>
                </Applicant>';
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

    /*
     * 获取客户性别
     * 通用 男 01 / 女 02
     * 太保 男 1  / 女 2  / 未知 0
     */
    private function getSex($sex, $identifyNumber)
    {
        if (!in_array($sex, ["01", "02"])) $sex = $this->utilService->getSexByIdCard($identifyNumber);
        if ($sex == "01") return 1;
        if ($sex == "02") return 2;
        return 0;
    }
}