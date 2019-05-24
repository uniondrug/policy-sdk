<?php

namespace Uniondrug\PolicySdk\Sdks\RenBaoCar\Modules;

trait Verify{
    public function verify(array $post){
        $xml_content = '<?xml version="1.0" encoding="utf-8"?>
        <PackageList>
        <Package>
            <Header>
                <RequestType>320</RequestType>
                <Version>2</Version>
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
                    <Inputs type="applicantInfo">
                        <Input name="applicantName">' . $post['ownerName'] . '</Input>
                        <Input name="applicantIdType">' . $post['ownerIdType'] . '</Input>
                        <Input name="applicantIdNo">' . $post['ownerIdNo'] . '</Input>
                        <Input name="applicantMobile">' . $post['ownerMobile'] . '</Input>
                    </Inputs>
                    <Inputs type="insuredInfo">
                        <Input name="insuredName">' . $post['insuredName'] . '</Input>
                        <Input name="insuredType">' . $post['insuredType'] . '</Input>
                        <Input name="insuredIdNo">' . $post['insuredIdNo'] . '</Input>
                        <Input name="insuredGender">' . $post['insuredGender'] . '</Input>
                        <Input name="insuredMobile">' . $post['insuredMobile'] . '</Input>
                    </Inputs>
                </InputsList>
                <VerifyCode>' . $post['VerifyCode'] . '</VerifyCode>
            </Request>
            <Sign></Sign>
        </Package>';
        $resultObj = $this->getCurl($xml_content);
        $data = [
            'header' => $resultObj['Package']['Header'],
            'data' => $resultObj['Package']['Response'],
        ];
        return $this->withData($data);
    }
}
