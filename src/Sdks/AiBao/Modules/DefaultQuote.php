<?php

namespace Uniondrug\PolicySdk\Sdks\AiBao\Modules;

trait DefaultQuote
{
    public function defaultQuote(array $post)
    {
        $postData = [
            'head' => [
                'transactionNo' => $post['transactionNo'],
                'aiBaoTransactionNo' => $post['aiBaoTransactionNo'],
                'operator' => $this->config->operator,
                'timeStamp' => date('Y-m-d H:i:s',time()),
                'errorCode' => '0000',
                'errorMsg' =>  '成功',
            ],
            'body' => [
                "insuredInfo" => [
                    "birthday" => $post['insuredBirthday'],
                    "idNo" => $post['insuredIdNo'],
                    "idType" => $this->convertIdentifyType($post['insuredIdType']),
                    "idName" => $post['insuredIdName'],
                    "sex" => $post['insuredGender'],
                    "mobile" => $post['insuredMobile'],
                    "mobileHolederName" => $post['insuredMobileHolederName'],
                    "mobileHolederIdType" => $this->convertIdentifyType($post['insuredMobileHolederIdType']) ,
                    "mobileHolederIdNo" => $post['insuredMobileHolederIdNo'],
                    "email" => $post['insuredEmail'] ?? '',
                    "address" => $post['insuredAddress'] ?? '',
                ],
                "carOwnerInfo" => [
                    "birthday" => $post['ownerBirthday'],
                    "idNo" => $post['ownerIdNo'],
                    "idType" => $this->convertIdentifyType($post['ownerIdType']),
                    "idName" => $post['ownerName'],
                    "sex" => $post['ownerGender'],
                    "mobile" => $post['ownerMobile'],
                    "email" => $post['carOwnerEmail'] ?? '',
                    "address" => $post['carOwnerAddress'] ?? '',
                ],
                "carInfo" => [
                    "vehicleCode" => $post['vehicleCode'] ?? '',
                    "frameNo" => strtoupper($post['vehicleFrameNo']),
                    "engineNo" => strtoupper($post['engineNo']),
                    "enrollDate" => $post['registerDate'],
                    "chgOwnerFlag" => $post['specialCarFlag'] ?? '0',
                    "transferDate" => $post['transferDate'] ?? '',
                    "isLoanVehicleFlag" => $post['loanFlag'] ?? '0',
                    "modelCode" => $post['modelCode'] ?? '',
                    "standardName" => $post['standardName'] ?? '',
                    "seatCount" => $post['vehicleSeats'] ?? '',
                    "brandName" => $post['brandName'] ?? '',
                    "familyName" => $post['familyName'] ?? '',
                    "countryNature" => $post['countryNature'] ?? '',
                    "tonCount" => $post['tonCount'] ?? '',
                    "exhaustScale" => $post['exhaustScale'] ?? '',
                    "wholeWeight" => $post['wholeWeight'] ?? '',
                    "purchasePrice" => $post['purchasePrice'] ?? '',
                    "fuelType" => $post['fuelType'] ?? '',
                    "remark" => $post['remark'] ?? '',
                    "yearPattern" => $post['yearPattern'] ?? '',
                    "configName" => $post['configName'] ?? '',
                    "gearboxType" => $post['gearboxType'] ?? '',
                    "monopolycode" => $post['monopolycode'] ?? '',
                    "monopolyname" => $post['monopolyname'] ?? '',
                    "subMonopolyType" => $post['subMonopolyType'] ?? '',
                ],
                "extendInfo" => [
                    "buyCarDate" => $post['buyCarDate  '] ?? '', //购车发票日期，上海地区 新车未上牌必传 YYYY-MM-DD
                    "vehicleType" => $post['vehicleType'] ?? '',
                    "traveltaxAddress" => $post['traveltaxAddress'] ?? '',
                    "carproofdate" => $post['carproofdate'] ?? '',
                    "fueltype" => $post['fueltype'] ?? '',
                    "carprooftype" => $post['carprooftype'] ?? '',
                    "carproofno" => $post['carproofno'] ?? '',
                ],
                "mainInfo" => [
                    "busiStartDate" => $post['bizBeginDate'] ?? '',
                    "busiEndDate" => $post['forceBeginDate'] ?? '',
                    "bzStartDate" => $post['forceBeginDate'] ?? '',
                    "bzEndDate" => $post['forceEndDate'] ?? '',
                    "effectiveImmediatelyFlag" => $post['effectiveImmediatelyFlag'] ?? '',
                    "beneficiary" => $post['loanName'] ?? '',
                    "insurenceCode" => $this->config->insurenceCode,
                    "bzVerifyCode" => $post['checkCodeCI'] ?? '',
                    "busiVerifyCode" => $post['checkCode'] ?? '',
                ]
            ]
        ];
        $postJson = json_encode($postData, JSON_UNESCAPED_UNICODE);
        $this->logger->defaultQuote()->info("保司请求报文:" . $postJson);
        $header = ['Content-Type: application/json'];
        $url = $this->setUrl('100071');
        try {
            $result = $this->curl_https($url, $postJson, $header, __FUNCTION__,'120');
        } catch (\Exception $e) {
            return $this->withError($e->getMessage());
        }
        $this->logger->defaultQuote()->info("保司响应报文:" . $result);
        $resultObj = json_decode($result,true);
        return $this->returnRes($resultObj);
    }
}