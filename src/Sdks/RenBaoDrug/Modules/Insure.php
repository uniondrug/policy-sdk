<?php

namespace Uniondrug\PolicySdk\Sdks\RenBaoDrug\Modules;

trait Insure
{
    public function insure(array $post)
    {
        $waterNo =$post['billNo'] ? $post['billNo']: $this->createUniqueWaterNo();
        $Md5Value = md5($waterNo .  $post['sumPremium'] . $this->config->token);
        $xml_content = '<?xml version="1.0" encoding="UTF-8"?><ApplyInfo>
    <GeneralInfo>
        <UUID>' . $waterNo . '</UUID>
        <PlateformCode>CPI000632</PlateformCode>
        <Md5Value>' . $Md5Value . '</Md5Value>
    </GeneralInfo>
    <PolicyInfos>
        <PolicyInfo>
            <SerialNo>1</SerialNo>
            <RiskCode>' . $post['riskCode'] . '</RiskCode>
            <OperateTimes>' . $post['operateTimes'] . '</OperateTimes>
            <StartDate>' . date('Y-m-d', strtotime($post['startDate'])) . '</StartDate>
            <EndDate>' . date('Y-m-d', strtotime($post['endDate'])) . '</EndDate>
            <StartHour>' . date('H', strtotime($post['startDate'])) . '</StartHour>
            <EndHour>' . date('H', strtotime($post['endDate'])) . '</EndHour>
            <startTime>' . date('i:s', strtotime($post['startDate'])) . '</startTime>
            <SumAmount>' . $post['sumAmount'] . '</SumAmount>
            <SumPremium>' . $post['sumPremium'] . '</SumPremium>
            <ArguSolution>1</ArguSolution>
            <Quantity>' . $post['quantity'] . '</Quantity>
            <InsuredPlan>
                <RationType>' . $post['rationType'] . '</RationType>
                <Schemes>
                    <Scheme>
                        <SchemeCode>1</SchemeCode>
                        <SchemeAmount>' . $post['sumAmount'] . '</SchemeAmount>
                        <SchemePremium>' . $post['sumPremium'] . '</SchemePremium>
                    </Scheme>
                </Schemes>
            </InsuredPlan>
            <LiabInfo>
                <NowTurnOver>' . $post['nowTurnOver'] . '</NowTurnOver>
            </LiabInfo>
            <Applicant>
                <AppliName>' . $post['policyName'] . '</AppliName>
                <AppliIdType>' . $this->convertIdentifyType($post['policyIdentifyType']) . '</AppliIdType>
                <AppliIdNo>' . $post['policyIdentifyNumber'] . '</AppliIdNo>
                <AppliIdentity>' . $post['policyIdentity'] . '</AppliIdentity>
                <AppliIdMobile>' . $post['policyMobile'] . '</AppliIdMobile>
                <AppliAddress>' . $post['policyAddress'] . '</AppliAddress>
            </Applicant>
            <Insureds>
                <Insured>
                    <InsuredSeqNo>1</InsuredSeqNo>
                    <InsuredName>' . $post['insuredName'] . '</InsuredName>
                    <InsuredIdType>' .$this->convertIdentifyType($post['insuredIdentifyType'])  . '</InsuredIdType>
                    <InsuredIdNo>' . $post['insuredIdentifyNumber'] . '</InsuredIdNo>
                    <InsuredIdMobile>' . $post['insuredIdMobile'] . '</InsuredIdMobile>
                    <InsuredAddress>' . $post['insuredAddress'] . '</InsuredAddress>
                </Insured>
            </Insureds>
            <Specials>
                <Special key="businessDepartmentCode">' . $post['businessDepartmentCode'] . '</Special>
            </Specials>
            <ExtendInfos>
                <ExtendInfo key=\'postCode_T\'>' . $post['policyPostCode'] . '</ExtendInfo>
                <ExtendInfo key=\'postCode_B\'>' . $post['insuredPostCode'] . '</ExtendInfo>
                <ExtendInfo key=\'linker_T\'>' . $post['policyContacter'] . '</ExtendInfo>
                <ExtendInfo key=\'linker_B\'>' . $post['insuredContacter'] . '</ExtendInfo>
                <ExtendInfo key=\'url\'>' . $post['url'] . '</ExtendInfo>
            </ExtendInfos>
        </PolicyInfo>
    </PolicyInfos>
</ApplyInfo>';

        $postData = [
            'interfaceNo' => '001001',
            'datas' => $xml_content
        ];
        $url = $this->config->insure;
        $client = new \nusoap_client($url, 'wsdl');
        $client->soap_defencoding = 'utf-8';
        $client->decode_utf8 = false;
        $client->xml_encoding = 'utf-8';
        $this->logger->insure()->info("保司请求报文:" . $xml_content);
        $result = $client->call('insureService', $postData);
        $resultXml = $result['return'];
        $this->logger->insure()->info("保司响应报文:" . $resultXml);
        if ($err = $client->getError()) {
            return $this->withError($err);
        }
        $resultObj = xml_to_array($resultXml, 'GB2312');
        if ($resultObj['GeneralInfoReturn']['ErrorCode'] != '00') {
            return $this->withError($resultObj['GeneralInfoReturn']['ErrorMessage']);
        }
        $returnObj = $resultObj['PolicyInfoReturns']['PolicyInfoReturn'];
        if ($returnObj['SaveResult'] != '00') {
            return $this->withError($returnObj['SaveMessage']);
        }
        $data = [
            'policyNo' => $returnObj['PolicyNo'],
            'epolicyAddress' => urlencode(urldecode($returnObj['DownloadUrl'])),
            'transTime' => $returnObj['SaveTimes'] ?: date("Y-m-d H:i:s"),
            'batchNo' => $waterNo,

        ];
        return $this->withData($data);
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
                $type = "10";
                break;
            //  台湾通行证
            case "05":
                $type = "09";
                break;
            //  驾驶证
            case "06":
                $type = "05";
                break;
            //  出生证
            case "07":
                $type = "02";
                break;
            //  外国人居留证
            case "08":
                $type = "16";
                break;
            //  组织机构代码
            case "09":
                $type = "31";
                break;
            default:
                $type = "99";
                break;
        }
        return $type;
    }
}