<?php

namespace Uniondrug\PolicySdk\Sdks\SuNational\Modules;

trait FeeUpload
{
    public function feeUpload(array $post)
    {
        //拼接数据
        $tansRefGUID = $this->guid();
        $time = date('Y-m-d H:i:s',time());
        $token = $this->config->dataSourceToken;
        $correlationId = sha1($token.$tansRefGUID.$time);
        $head = [
            'StandardVersionCode'=>$this->config->yiVersionCod,
            'TransRefGUID'=>$tansRefGUID,
            'TransactionCode'=>$this->config->BillTransactionCode,
            'TransactionSubCode'=>$this->config->BillTransactionSubCode,
            'CorrelationId'=>$correlationId,
            'MessageDateTime'=>$time,
            'BusinessType'=>'3',
            'SenderCode'=>$this->config->senderCode,
            'ReceiverCode'=>$this->config->ReceiverCode,
        ];
        $params = [
            'FeeSettlement'=>[
                'MedicalGUID'=>$post['MedicalGUID'],//就医唯一识别码
                'TpaFlowCode'=>$this->config->TpaFlowCode,//第三方管理的流程代码
                'HospitalSettlement'=>[
                    'PaymentTarget'=>$this->config->UploadTarget,//付款对象 1
                    'SettlementWay'=>'',//补偿结算方式
                    'MedicalInstituteCode'=>$this->config->MedicalInstituteCode,//医疗机构代码 320500001759
                    'SettlementSerialNumber'=>$post['MedicalGUID'],//就医结算唯一编号与MedicalGUID一致
                    'MedicalType'=>$this->config->MedicalType,//就医类型 14
                    'LeaveHospitalStyle'=>$this->config->LeaveHospitalStyle,//离院方式 1
                    'LeaveHospitalState'=>$this->config->LeaveHospitalState,//离院状态 1
                    'OffsiteMedicalSign'=>$this->config->OffsiteMedicalSign,//异地就医标识 0
                    'ReferralPaymentRatio'=>'',//转诊补偿比例
                    'LeaveHospitalDiagnosisCode'=>$this->config->LeaveHospitalDiagnosisCode,//离院诊断疾病 X
                    'LeaveHospitalDiagnosisName'=>$this->config->LeaveHospitalDiagnosisName,//离院诊断疾病名称 未知疾病
                    'MedicalInsuranceSettleDate'=>$post['MedicalInsuranceSettleDate'],//社保结算时间 药店的结算时间
                    'SettleYear'=>$this->config->SettleYear,//结算年度 填当前年度：2019
                    'TotalAmount'=>$post['majorList']['lossList']['sumClaim'],//医疗总费用 权益理赔金额
                    'MedicalInsurancePayment'=>isset($post['MedicalInsurancePayment'])?$post['MedicalInsurancePayment']:0,//社保补偿总金额 0
                    'MedicalInsuranceCompliantAmount'=>isset($post['MedicalInsuranceCompliantAmount'])?$post['MedicalInsuranceCompliantAmount']:0,//社保合规费用 0
                    'BusinessInsurancePayment'=>isset($post['BusinessInsurancePayment'])?$post['BusinessInsurancePayment']:0,//大病保险总补偿金额 0
                    'PartialSelfPayment'=>isset($post['PartialSelfPayment'])?$post['PartialSelfPayment']:0,//乙类自付费用 0
                    'PaymentBySelf'=>isset($post['PaymentBySelf'])?$post['PaymentBySelf']:0,//丙类全自费费用 0
                    'RcptInfoLst'=>[
                        'RcptNo'=>$post['RcptNo'],//票据号 6到25位的数字组成，需保证每次订单唯一
                        'MIcd10Code'=>'X',//疾病主诊断代码
                        'Micd10Name'=>'未知疾病',//疾病主诊断名称
                        'BeginDate'=>isset($post['BeginDate']) ? $post['BeginDate'] :'',//开始日期 理赔发生时间
                        'EndDate'=>isset($post['EndDate']) ? $post['EndDate'] :'',//结束日期 理赔发生时间
                        'ExpenMode'=>$this->config->ExpenMode,//诊疗方式 14
                        'RcptAmnt'=>$post['majorList']['lossList']['sumClaim'],//票据总金额 同医疗总费用
                    ]
//                    'RcptInfoLst'=>$post['RcptList'],
                ],
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
        $this->logger->feeUpload()->info("苏州国寿票据上传请求报文:" . $postJson);
        try {
            $result = $this->curl_https($this->config->requestUrl, $postJson, $header, __FUNCTION__,30);
        } catch (\Exception $e) {
            return $this->withError($e->getMessage());
        }
        $this->logger->feeUpload()->info("国寿票据上传响应报文:" . $result);
        $resultArr = json_decode($result);
        $returnData = $resultArr->result->root->body->FeeSettlementResponse->BusinessProcessStatus;
        if ($returnData->BusinessStatus!=1) {
            $msg = $returnData->BusinessMessage;
            return $this->withError($msg);
        }
        return $this->withData(['status'=>$returnData->BusinessStatus]);
    }
}
