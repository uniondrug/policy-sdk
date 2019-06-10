<?php

namespace Uniondrug\PolicySdk\Sdks\RenBaoDrug\Modules;

trait Claim
{
    public function claim(array $post)
    {
        $md5Value = md5($post['policyNo'].$this->config->sourceFlag.$this->config->policySourceType);
        $xml_content = '<?xml version="1.0" encoding="utf-8"?>
<PACKET type="RESPONSE" version="1.0">
    <RequestHead>
        <Uuid>'.$post['waterNo'].'</Uuid>
        <Sender>'.$this->config->sender.'</Sender>
        <User>'.$this->config->user.'</User>
        <Password>'.$this->config->psssword.'</Password>
        <FlowinTime>'.date('Y-m-d H:i:s').'</FlowinTime>
        <RequestType>'.$this->config->claimRequestType.'</RequestType>
        <ServerVersion>'.$this->config->serverVersion.'</ServerVersion>
        <Token>'.$md5Value.'</Token>
    </RequestHead>
    <RequestBody>
        <BuscaseInfoVo>
            <PolicyNo>'.$post['policyNo'].'</PolicyNo>
            <ReportorName>'.$this->config->reportorName.'</ReportorName>
            <ReportorNumber>'.$this->config->reportorNumber.'</ReportorNumber>
            <ContactName>'.$this->config->contactName.'</ContactName>
            <ContactTel>'.$this->config->contactTel.'</ContactTel>
            <ContactNumber>'.$this->config->contactNumber.'</ContactNumber>
            <RelationShip>000</RelationShip>
            <SourceFlag>'.$this->config->sourceFlag.'</SourceFlag>
            <PolicySourceType>'.$this->config->policySourceType.'</PolicySourceType>
        </BuscaseInfoVo>
        <MajorInfoList>
            <MajorInfoVo>
                <DamageFlag>'.$post['majorList']['damageFlag'].'</DamageFlag>
                <DamageTime>'.$post['majorList']['damageTime'].'</DamageTime>
                <DamageAddress>'.($post['majorList']['damageAddress'] ?: $this->config->insuredAddress).'</DamageAddress>
                <DamageAreaCode>'. ($post['majorList']['damageAreaCode'] ?: "11" ).'</DamageAreaCode>
                <DamageAreaProvinceCode>'.($post['majorList']['damageAreaProvinceCode'] ?: '310000').'</DamageAreaProvinceCode>
                <DamageAreaCityCode>'.($post['majorList']['damageAreaCityCode'] ?: '310100').'</DamageAreaCityCode>
                <DamageReasonCode>'.($post['majorList']['damageReasonCode'] ?: 'A10054').'</DamageReasonCode>
                <DamageReasonName>'.($post['majorList']['damageReasonName'] ?: '扩展责任').'</DamageReasonName>
                <Remark>'.$post['majorList']['remark'].'</Remark>
                <Currency>CNY</Currency>
                <LossList>
                    <LossVo>
                        <SumClaim>'.$post['majorList']['lossList']['sumClaim'].'</SumClaim>
                        <SerialNo>'.($post['majorList']['lossList']['serialNo'] ?: '1').'</SerialNo>
                        <LossName></LossName>
                        <LossFeeType>05</LossFeeType>
                        <FeeTypeCode>98</FeeTypeCode>
                        <SumRest>'.($post['majorList']['lossList']['sumRest'] ?: '0') .'</SumRest>
                        <SumLoss>'.($post['majorList']['lossList']['sumLoss']?:$post['majorList']['lossList']['sumClaim']) .'</SumLoss>
                        <SumRealPay>'.($post['majorList']['lossList']['sumRealPay']?:$post['majorList']['lossList']['sumClaim']).'</SumRealPay>
                        <ClaimRate>'.($post['majorList']['lossList']['claimRate'] ?: '100').'</ClaimRate>
                        <DeductibleRate>'.($post['majorList']['lossList']['deductibleRate'] ?: '0').'</DeductibleRate>
                        <Deductible>'.($post['majorList']['lossList']['deductible'] ?: '0').'</Deductible>
                        <OutsysPayeeId>1</OutsysPayeeId>
                    </LossVo>
                </LossList>
                <PayPersonList>
                    <PayPerson>
                        <PublicAccountFlag>'.$this->config->publicAccountFlag.'</PublicAccountFlag>
                        <CustomerTypeCode>01</CustomerTypeCode>
                        <IsPrivate>2</IsPrivate>
                        <Telephone>'.$this->config->payTelephone.'</Telephone>
                        <PayeeName>'.$this->config->payeeName.'</PayeeName>
                        <IdentifyType>'.$this->convertIdentifyType($this->config->payIdentifyType).'</IdentifyType>
                        <IdentifyNumber>'. $this->config->payIdentifyNumber.'</IdentifyNumber>
                        <AccountName>'. $this->config->accountName.'</AccountName>
                        <OpenBankCode>'. $this->config->openBankCode.'</OpenBankCode>
                        <BankAccount>'. $this->config->bankAccount.'</BankAccount>
                        <OutsysPayeeId>1</OutsysPayeeId>
                        <SpecialOptionCode></SpecialOptionCode>
                        <SpecialOptionName></SpecialOptionName>
                        <ProfessionCode></ProfessionCode>
                        <ProfessionName></ProfessionName>
                    </PayPerson>
                </PayPersonList>
                <CheckVo>
                    <Context>根据保单保险责任，保险标的的状态及实际销售情况，该保单符合理赔条件。</Context>
                    <Context1></Context1>
                    <Context2></Context2>
                    <Context3></Context3>
                    <DelayedReporReason></DelayedReporReason>
                </CheckVo>
            </MajorInfoVo>
        </MajorInfoList>
        <ExtraVo>
        </ExtraVo>
    </RequestBody>
</PACKET>';

        $this->logger->claim()->info("保司请求报文:" . convert_encoding($xml_content));
        $header = ['Content-Type: application/x-www-form-urlencoded'];;
        try {
            $result = $this->curl_https($this->config->claimUrl, $xml_content, $header, __FUNCTION__,60);
            $this->logger->claim()->info("保司响应报文:" . convert_encoding($result));
            $resultArray = xml_to_array($result,'GBK');
        } catch (\Exception $e) {
            return $this->withError($e->getMessage());
        }
        if($resultArray['ResponseBody']['ErrorMessage'] != '0'){
            return $this->withError($resultArray['ResponseBody']['ErrorMessage']);
        }
        //验签
//        $this->checkToken($resultArray);
        return $this->withData([
            'waterNo' => $resultArray['ResponseHead']['Uuid'],
            'registNo' => $resultArray['ResponseBody']['RegistList']['RegistVo']['RegistNo'],
            'damageFlag' => $resultArray['ResponseBody']['RegistList']['RegistVo']['DamageFlag']

        ]);
    }

    //校验返回值token
    private function checkToken($resultArray){
        $str = $resultArray['ResponseHead']['Uuid'];
        foreach ($resultArray['ResponseBody']['RegistList']['RegistVo'] as $regist){
            $str .= $regist['RegistNo'];
        }
        if(md5($str) != $resultArray['ResponseHead']['Token']){
            throw new \Exception("token验证失败");
        }
        return true;
    }
}