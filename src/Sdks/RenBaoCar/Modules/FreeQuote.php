<?php

namespace Uniondrug\PolicySdk\Sdks\RenBaoCar\Modules;

trait FreeQuote{
    public function freeQuote(array $post){
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
