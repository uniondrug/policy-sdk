<?php

namespace Uniondrug\PolicySdk\Sdks\SuNational\Modules;

trait SuClaim
{
    public function suClaim(array $post)
    {
        //拼接数据
        $tansRefGUID = $this->guid();
        $time = date('Y-m-d H:i:s',time());
        $token = $this->config->dataSourceToken;
        $correlationId = sha1($token.$tansRefGUID.$time);
        $head = [
            'StandardVersionCode'=>$this->config->yiVersionCode,
            'TransRefGUID'=>$tansRefGUID,
            'TransactionCode'=>$this->config->ClaimTransactionCode,
            'TransactionSubCode'=>$this->config->ClaimTransactionSubCode,
            'CorrelationId'=>$correlationId,
            'MessageDateTime'=>$time,
            'SenderCode'=>$this->config->senderCode,
            'ReceiverCode'=>$this->config->ReceiverCode,
        ];

        $post['GenderCode'] = $this->utilService->getSexByIdCard($post['PersonGUID']);
        $post['Birthday'] = $this->utilService->getBirthByIdCard($post['PersonGUID']);
        $params = [
            'HospitalRegistration'=>[
                'InstituteCode'=>'',//归属社保机构编码
                'BelongInstituteAreaCode'=>$post['BelongInstituteAreaCode'],//归属社保地区代码
                'MedicalInstituteCode'=>$this->config->MedicalInstituteCode,//医疗机构代码
                'MedicalInstituteName'=>$this->config->MedicalInstituteName,//医疗机构名称
                'TpaFlowCode'=>$this->config->TpaFlowCode,//第三方管理的流程代码
                'PersonGUID'=>$post['PersonGUID'],//个人唯一识别码
                'PersonalIdentification'=>$this->config->PersonalIdentification,//人员身份
                'MedicalGUID'=>$post['MedicalGUID'],//就医唯一识别码
                'MedicalType'=>$this->config->MedicalType,//就医类型
                'AccidentReason'=>$this->config->AccidentReason,//出险原因
                'SocialMedicareType'=>$this->config->SocialMedicareType,//社会医疗保险类别
                'PatientType'=>'',//患者类型
                'MedicalDepartment'=>'',//就诊科室
                'MainDiagnosisCode'=>$this->config->MainDiagnosisCode,//主诊断疾病
                'MainDiagnosisName'=>$this->config->MainDiagnosisName,//主诊断疾病名称
                'SecondaryDiagnosisCode'=>$this->config->SecondaryDiagnosisCode,//第二诊断疾病
                'MedicalDate'=>$post['majorList']['damageTime'],//MedicalDate
                //人员五要素
                'Name'=>$post['name'],//出险人姓名
                'GenderCode'=>$post['GenderCode']==02 ? 'F' : 'M',//出险人性别 F M
                'CredentialType'=>'I',//出险人证件类型  默认身份证 I
                'CredentialNum'=>$post['PersonGUID'],//出险人证件号码
                'Birthday'=>$post['Birthday'],//出险人出生日期
            ]
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
        $this->logger->suClaim()->info("苏州国寿请求报文:" . $postJson);
        try {
            $result = $this->curl_https($this->config->requestUrl, $postJson, $header, __FUNCTION__);
        } catch (\Exception $e) {
            return $this->withError($e->getMessage());
        }
        $this->logger->suClaim()->info("就医登记响应报文:" . $result);
        $resultArr = json_decode($result);
        $resultData  = $resultArr->result->root->body->HospitalRegistrationResponse->BusinessProcessStatus;
        if ($resultData->BusinessStatus!=1) {
            $msg = $resultData->BusinessMessage;
            return $this->withError($msg);
        }
        return $this->withData(
            [
                'status'=>$resultData->BusinessStatus,
                'msg'=>$resultData->BusinessMessage,
                'ClaimNo'=>$resultData->ClaimNo,
            ]
        );
    }

    /**
     * 生成唯一的标识
     * @return string
     */
    public function guid(){
        if (function_exists('com_create_guid')){
            return com_create_guid();
        }else{
            mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);// "-"
            $uuid = chr(123)// "{"
                .substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid,12, 4).$hyphen
                .substr($charid,16, 4).$hyphen
                .substr($charid,20,12)
                .chr(125);// "}"
            return $uuid;
        }
    }
    //获取access_token
    public function getToken(){
            $header[] = 'Content-Type: application/json';
            $body = [
                'app_key' => $this->config->app_key,
                'app_secret' => $this->config->app_secret,
            ];
            $url = $this->config->tokenUrl;
            $postJson = json_encode($body);
            try {
                $response = $this->curl_https($url, $postJson, $header, __FUNCTION__);
                $result = json_decode($response, TRUE);
                if (is_array($result) && isset($result['result']['access_token'])) {
                    return $result['result']['access_token'];
                } else {
                    return '';
                }
            } catch (\Exception $e) {
                return $this->withError($e->getMessage());
            }
    }
}