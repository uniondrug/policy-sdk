<?php

namespace Uniondrug\PolicySdk\Sdks\SuNational\Modules;

trait CostSettle
{
    public function costSettle(array $post)
    {
        //拼接数据
        $tansRefGUID = $this->guid();
        $time = date('Y-m-d H:i:s',time());
        $token = $this->config->dataSourceToken;
        $correlationId = sha1($token.$tansRefGUID.$time);
        $head = [
            'StandardVersionCode'=>$this->config->StandardVersionCode,
            'TransRefGUID'=>$tansRefGUID,
            'TransactionCode'=>$this->config->CostTransactionCode,
            'TransactionSubCode'=>$this->config->CostTransactionSubCode,
            'CorrelationId'=>$correlationId,
            'MessageDateTime'=>$time,
            'SenderCode'=>$this->config->senderCode,
            'ReceiverCode'=>$this->config->ReceiverCode,
        ];
        $params = [
            'FeeSettlement'=>[
                'MedicalGUID'=>$post['MedicalGUID'],
                'TpaFlowCode'=>$this->config->TpaFlowCode,
                'HospitalSettlement'=>[
                    'PaymentTarget'=>3,//付款对象 3
                    'SettlementWay'=>'',
                    'MedicalInstituteCode'=>$this->config->MedicalInstituteCode,
                    'SettlementSerialNumber'=>$post['MedicalGUID'],
                    'MedicalType'=>$this->config->MedicalType,
                    'LeaveHospitalStyle'=>$this->config->LeaveHospitalStyle,
                    'LeaveHospitalState'=>$this->config->LeaveHospitalState,
                    'OffsiteMedicalSign'=>$this->config->OffsiteMedicalSign,
                    'ReferralPaymentRatio'=>'',
                    'LeaveHospitalDiagnosisCode'=>$this->config->LeaveHospitalDiagnosisCode,
                    'LeaveHospitalDiagnosisName'=>$this->config->LeaveHospitalDiagnosisName,
                    'MedicalInsuranceSettleDate'=>$post['MedicalInsuranceSettleDate'],
                    'SettleYear'=>$this->config->SettleYear,
                    'TotalAmount'=>$post['majorList']['lossList']['sumClaim'],
                    'MedicalInsurancePayment'=>isset($post['MedicalInsurancePayment'])?$post['MedicalInsurancePayment']:0,
                    'MedicalInsuranceCompliantAmount'=>isset($post['MedicalInsuranceCompliantAmount'])?$post['MedicalInsuranceCompliantAmount']:0,
                    'BusinessInsurancePayment'=>isset($post['BusinessInsurancePayment'])?$post['BusinessInsurancePayment']:0,
                    'PartialSelfPayment'=>isset($post['PartialSelfPayment'])?$post['PartialSelfPayment']:0,
                    'PaymentBySelf'=>isset($post['PaymentBySelf'])?$post['PaymentBySelf']:0,
                    'RcptInfoLst'=>[
                        'RcptNo'=>$post['RcptNo'],
                        'RcptAmnt'=>$post['majorList']['lossList']['sumClaim'],
                        'SocialPayAmnt'=>0,
                        'SocialInAmnt'=>isset($post['SocialInAmnt'])?$post['SocialInAmnt']:0,
                        'SelfBAmnt'=>isset($post['SelfBAmnt'])?$post['SelfBAmnt']:0,
                        'SelfCAmnt'=>isset($post['SelfCAmnt'])?$post['SelfCAmnt']:0,
                        'OtherSplitAmnt'=>isset($post['OtherSplitAmnt'])?$post['OtherSplitAmnt']:0,
                        'CisPayAmnt'=>isset($post['CisPayAmnt'])?$post['CisPayAmnt']:0,
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
        $this->logger->costSettle()->info("苏州国寿结算请求报文:" . $postJson);
        try {
            $result = $this->curl_https($this->config->requestUrl, $postJson, $header, __FUNCTION__,120,0);
        } catch (\Exception $e) {
            return $this->withError($e->getMessage());
        }
        $this->logger->costSettle()->info("国寿结算响应报文:" . $result);
        $resultArr = json_decode($result);
        $resultData =$resultArr->result->root->body->FeeSettlementResponse->BusinessProcessStatus;
        if ($resultData->BusinessStatus!=1) {
            $msg = $resultData->BusinessMessage;
            return $this->withError($msg);
        }
        return $this->withData(['status'=>$resultData->BusinessStatus,'CntrInfoLst'=>$resultArr->result->root->body->FeeSettlementResponse->CntrInfoLst,'ClaimNo'=>$resultArr->result->root->body->FeeSettlementResponse->ClaimNo]);
    }
}
