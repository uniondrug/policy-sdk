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
                        <Input name="ownerName">' . $post['ownerName'] . '</Input>
                        <Input name="ownerIdType">' . $post['ownerIdType'] . '</Input>
                        <Input name="ownerIdNo">' . $post['ownerIdNo'] . '</Input>
                        <Input name="ownerMobile">' . $post['ownerMobile'] . '</Input>
                    </Inputs>
                    <Inputs type="insuredInfo">
                        <Input name="applicantName">' . $post['applicantName'] . '</Input>
                        <Input name="applicantIdType">' . $post['applicantIdType'] . '</Input>
                        <Input name="applicantIdNo">' . $post['applicantIdNo'] . '</Input>
                        <Input name="applicantMobile">' . $post['applicantMobile'] . '</Input>
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
