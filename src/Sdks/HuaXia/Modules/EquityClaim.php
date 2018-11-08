<?php
/**
 * Created by PhpStorm.
 * User: yaolian
 * Date: 2018/11/7
 * Time: 22:38
 */
namespace Uniondrug\PolicySdk\Sdks\HuaXia\Modules;

trait EquityClaim
{
    public function equityClaim(array $post)
    {
        $transNo =$this->createUniqueWaterNo();
        $postData['Head'] = [
            "ServiceID"=> "medUnion001",
            "TransNo"=> $transNo,
            "FromSystemkey"=> "zt",
            "TransDate"=> date("Y-m-d"),
            "TransTime"=> date("H-i-s"),
        ];

        $postData['Body'] = [
            "BillNo"=> $post['billNo'],
            "BatchNo"=> $post['batchNo'],
            "BatchPersonNum"=> $post['totalCts'],
            "BatchSumAmount"=> $post['totalAmt'],
            "BatchSumNum"=> $post['totalTransNum'],
            "TransNo"=> $transNo,
            "TransNum"=> $post['transNum'],
            "TransPersonNum"=> $post['transPersonNum'],
            "TransAmount"=> $post['transAmount'],
            "GrpContNo"=> $post['tradeNo'],
            "RiskCode"=> "211708",
        ];
        $this->getPayee($post,$postData);
        $this->getClientInfo($post,$postData);
        $postJson = json_encode($postData, JSON_UNESCAPED_UNICODE);

        $this->logger->equityClaim()->info("保司请求报文:" . $postJson);
        $header[] = 'Content-Type: application/json';
        $header[] = 'MethodName: llylClaim';
        try {
            $result = $this->curl_https($this->config->equityClaim, $postJson, $header, __FUNCTION__);
        } catch (\Exception $e) {
            return $this->withError($e->getMessage());
        }
        $this->logger->equityClaim()->info("保司响应报文:" . $result);
        $resultObj = json_decode($result);
        if ($resultObj->Head->ResultCode != "0") {
            return $this->withError($resultObj->Head->ResultMessage);
        }
        $data = [
            'BillNo' => $resultObj->Body->BillNo,
            'BatchNo' => $resultObj->Body->BatchNo,
            'TransNo' => $resultObj->Body->TransNo,
            'BillInfos' => $resultObj->Body->BillInfos,
        ];
        return $this->withData($data);
    }

    private function getPayee($post=[],&$postData=[]){
        $data = [
            "PayeeType"=> $post['payeeType'],
            "PayeeIDType"=> $post['payeeIdType'],
            "PayeeIdNo"=> $post['payeeIdNo'],
            "PayeeName"=> $post['payeeName'],
            "PayType"=> $post['payType'],
            "HeadBankCode"=> $post['InAcctBankCode'],
            "HeadBankName"=> $post['InAcctBankName'],
            "BankProvince"=> $post['InAcctProvinceName'],
            "BankCity"=> $post['InAcctCityName'],
            "BankIDNo"=> $post['InAcctNo'],
        ];
        $postData['Body']=array_merge($postData['Body'], $data);
    }

    /**
     * 客户信息
     */
    private function getClientInfo($post,&$postData=[]){
        foreach($post['BillInfos'] as $model){
            $data[] =[
                "ClientNo"=> $model['clientNo'],
                "Name"=> $model['clientName'],
                "Birthday"=> $model['clientBirthday'],
                "IDType"=> $model['clientIdType'],
                "IDNo"=> $model['clientIdNo'],
                "RgtDate"=> $model['claimTime'],
                "Mobile"=>$model['clientMobile'],
                "AccReason"=> $model['claimReason'],
                "InvoiceNo"=> $model['invoiceNo'],
                "FeeType"=> 'A',
                "HospitalNo"=> $model['hospitalName'],
                "BillMoney"=> $model['billAmount'],
                "StartDate"=> $model['billTime'],
                "TyinSurance"=> $model['insuranceType'],
                "ClaimaccList"=> $model['aegerDetail'],
                "ClaimOperationList"=> $model['aegerDesc'],
                "BnfStartDate"=> "",
                "Nationality"=> $model['bnfCountry'],
                "Bnfphone"=> $model['bnfMobile'],
                "BnfProvince"=> $model['bnfProvinceName'],
                "BnfCity"=> $model['bnfCityName'],
                "BnfCounty"=> $model['bnfAreaName'],
                "BnfAddress"=> "",
                "RevenueStatus"=> '00',
            ];
        }
        $postData['Body']['BillInfos'] = $data;
    }

}