<?php

namespace Uniondrug\PolicySdk\Sdks\RenBaoCar\Modules;

trait FreeQuote{
    public function freeQuote(array $post){
        $request_content = '
                <Request>
                    <InputsList>
                        <Inputs type="optional">
                            <Input name="bizFlag">'.$post['bizFlag'].'</Input>
                            <Input name="forceFlag">'.$post['forceFlag'].'</Input>
                            <Input name="P110000">'.$post['P110000'].'</Input>
                            <Input name="P120000">'.$post['P120000'].'</Input>
                            <Input name="P130000">'.$post['P130000'].'</Input>
                            <Input name="P140000">'.$post['P140000'].'</Input>
                            <Input name="P150000">'.$post['P150000'].'</Input>
                            <Input name="P110100">'.$post['P110100'].'</Input>
                            <Input name="P110200">'.$post['P110200'].'</Input>
                            <Input name="P110300">'.$post['P110300'].'</Input>
                            <Input name="P110400">'.$post['P110400'].'</Input>
                            <Input name="P110500">'.$post['P110500'].'</Input>
                            <Input name="P110600">'.$post['P110600'].'</Input>
                            <Input name="P110700">'.$post['P110700'].'</Input>
                            <Input name="P110800">'.$post['P110800'].'</Input>
                            <Input name="P130200">'.$post['P130200'].'</Input>
                            <Input name="P130300">'.$post['P130300'].'</Input>
                            <Input name="P110001">'.$post['P110001'].'</Input>
                            <Input name="P130001">'.$post['P130001'].'</Input>
                            <Input name="P120001">'.$post['P120001'].'</Input>
                            <Input name="P110101">'.$post['P110101'].'</Input>
                            <Input name="P110501">'.$post['P110501'].'</Input>
                            <Input name="P140001">'.$post['P140001'].'</Input>
                            <Input name="P150001">'.$post['P150001'].'</Input>
                            <Input name="P110601">'.$post['P110601'].'</Input>
                            <Input name="P130201">'.$post['P130201'].'</Input>
                            <Input name="P010000">'.$post['P010000'].'</Input>
                        </Inputs>
                        <Inputs type="vehicleInfo">
                            <Input name="checkCode">'.$post['checkCode'].'</Input>
                            <Input name="checkCodeCI">'.$post['checkCodeCI'].'</Input>
                            <Input name="bizBeginDate">'.$post['bizBeginDate'].'</Input>
                            <Input name="bizBeginDateHour">'.$post['bizBeginDateHour'].'</Input>
                            <Input name="forceBeginDate">'.$post['forceBeginDate'].'</Input>
                            <Input name="forceBeginDateHour">'.$post['forceBeginDateHour'].'</Input>
                            <Input name="carShipFlag">'.$post['carShipFlag'].'</Input>
                        </Inputs>
                    </InputsList>
                </Request>';
        try {
            $resultArray = $this->getCurl($request_content,__FUNCTION__,$post['transactionNo'],'101110');
        } catch (\Exception $e) {
            return $this->withError($e->getMessage());
        }
        if ($resultArray['Package']['Header']['Status'] != 100 && $resultArray['Package']['Header']['Status'] != 600) {
            return $this->withError($resultArray['Package']['Header']['ErrorMessage'],$resultArray['Package']['Header']['Status']);
        }
        $data = [
            'header' => $resultArray['Package']['Header'],
            'data' => $resultArray['data'],
        ];
        return $this->withData($data);
        $xml_content = '<?xml version="1.0" encoding="utf-8"?>
        <PackageList>
            <Package>
                <Header>
                    <Version>2</Version>
                    <RequestType>101110</RequestType>
                    <InsureType>100</InsureType>
                    <SessionId>' . $post['transactionNo'] . '</SessionId>
                    <SellerId>123456</SellerId>
                    <From>PICCTEST</From>
                    <SendTime>' . date('Y-m-d H:i:s',time()) . '</SendTime>
                    <Status>100</Status>
                    <ErrorMessage></ErrorMessage>
                </Header>
                <Request>
                    <InputsList>
                        <Inputs type="optional">
                            <Input name="P110000">247000</Input>
                            <Input name="bizFlag">1</Input>
                            <Input name="forceFlag">1</Input>
                            <Input name="P130201">0</Input>
                            <Input name="P150000">0</Input>
                            <Input name="P140000">0</Input>
                            <Input name="P130000">500000</Input>
                            <Input name="P120000">247000</Input>
                            <Input name="P130200">0</Input>
                            <Input name="P110800">0</Input>
                            <Input name="P110601">0</Input>
                        </Inputs>
                        <Inputs type="vehicleInfo">
                            <Input name="checkCode"></Input>
                            <Input name="checkCodeCI"></Input>
                            <Input name="bizBeginDate">2017-11-26</Input>
                            <Input name="bizBeginDateHour">0</Input>
                            <Input name="forceBeginDate">2017-11-26</Input>
                            <Input name="forceBeginDateHour">0</Input>
                            <Input name="carShipFlag">1</Input>
                        </Inputs>
                    </InputsList>
                </Request>
                <Sign></Sign>
            </Package>
        </PackageList>';
        $resultObj = $this->getCurl($xml_content);
        $data = [
            'header' => $resultObj['Package']['Header'],
            'data' => $resultObj['Package']['Response'],
        ];
        return $this->withData($data);
    }
}
