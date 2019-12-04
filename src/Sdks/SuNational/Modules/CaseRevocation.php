<?php

namespace Uniondrug\PolicySdk\Sdks\SuNational\Modules;

trait CaseRevocation
{
    public function caseRevocation(array $post)
    {
        //拼接数据
        $tansRefGUID = $this->guid();
        $time = date('Y-m-d H:i:s',time());
        $token = $this->config->dataSourceToken;
        $correlationId = sha1($token.$tansRefGUID.$time);
        $head = [
            'StandardVersionCode'=>$this->config->StandardVersionCode,
            'TransRefGUID'=>$tansRefGUID,
            'TransactionCode'=>'S115',
            'TransactionSubCode'=>'02',
            'CorrelationId'=>$correlationId,
            'MessageDateTime'=>$time,
            'SenderCode'=>$this->config->senderCode,
            'ReceiverCode'=>$this->config->ReceiverCode,
        ];
        $params = [
            'HospitalSettlementCancel'=>[
                'InstituteCode'=>'',//归属社保机构编码
                'BelongInstituteAreaCode'=>$post['BelongInstituteAreaCode'],//归属社保地区代码
                'MedicalInstituteCode'=>$this->config->MedicalInstituteCode,//医疗机构代码
                'MedicalInstituteName'=>$this->config->MedicalInstituteName,//医疗机构名称
                'TpaFlowCode'=>$this->config->TpaFlowCode,//第三方管理的流程代码
                'PersonGUID'=>$post['PersonGUID'],//个人唯一识别码
                'PersonalIdentification'=>$this->config->PersonalIdentification,//人员身份
                'MedicalGUID'=>$post['MedicalGUID'],//就医唯一识别码
                'MedicalType'=>$this->config->MedicalType,//就医类型
                //人员五要素
                'Name'=>$post['name'],//出险人姓名
                'GenderCode'=>$post['GenderCode']==02 ? 'F' : 'M',//出险人性别 F M
                'CredentialType'=>'I',//出险人证件类型  默认身份证 I
                'CredentialNum'=>$post['PersonGUID'],//出险人证件号码
                'Birthday'=>$post['Birthday'],//出险人出生日期
            ],

        ];
        $info  = [
            'root'=>[
                'head'=>$head,
                'body'=>$params
            ],
        ];
        $access_token = $this->getToken();
        $header[] = 'Content-Type: application/json';
        $header[] = 'Authorization: Bearer '.$access_token;
        $postJson = json_encode($info, JSON_UNESCAPED_UNICODE);
        $this->logger->caseRevocation()->info("苏州国寿撤销请求报文:" . $postJson);
        try {
            $result = $this->curl_https($this->config->requestUrl, $postJson, $header, __FUNCTION__,30);
        } catch (\Exception $e) {
            return $this->withError($e->getMessage());
        }
        $this->logger->caseRevocation()->info("国寿撤销响应报文:" . $result);
        $resultArr = json_decode($result);
        $resultData =$resultArr->result->root->body->HospitalSettlementCancelResponse;
        return $this->withData(['result'=>$resultData]);
    }
}
