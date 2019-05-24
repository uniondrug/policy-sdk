<?php
namespace Uniondrug\PolicySdk\Sdks\RenBaoCar\Modules;

trait SuppleQuote{
    public function suppleQuote (array $post) {
        $xml_content = '<?xml version="1.0" encoding="utf-8"?>
        <PackageList>
        <Package>
            <Header>
                <Version>2</Version>
                <RequestType>101105</RequestType>
                <InsureType>100</InsureType>
                <SessionId>' . $post['transactionNo'] . '</SessionId>
                <SellerId>123456</SellerId>
                <From>DDINSURE</From>
                <SendTime>' . date('Y-m-d H:i:s',time()) . '</SendTime>
                <Status>100</Status>
                <ErrorMessage></ErrorMessage>
            </Header>
            <Request>
                <InputsList>
                    <Inputs type="vehicleInfo">
                        <Input name="vehicleBrand">' . $post['vehicleBrand'] . '</Input>
                        <Input name="vehicleFrameNo">' . $post['vehicleFrameNo'] . '</Input>
                        <Input name="engineNo">' . $post['vehicleFrameNo'] . '</Input>
                        <Input name="checkCode"></Input>
                        <Input name="specialCarFlag">' . $post['specialCarFlag'] . '</Input>
                        <Input name="registerDate">' . $post['registerDate'] . '</Input>
                        <Input name="checkCodeCI"></Input>
                       <Input name="checkCodeJS"></Input>
                        <Input name="vehicleModel">' . $post['vehicleModel'] . '</Input>
                        <Input name="specialCarDate">' . $post['specialCarDate'] . '</Input>
                        <Input name="buyCarDate">' . $post['buyCarDate'] . '</Input>
                    </Inputs>
                    <Inputs type="ownerInfo">
                        <Input name="ownerMobile">' . $post['ownerMobile'] . '</Input>
                        <Input name="ownerIdNo">' . $post['ownerIdNo'] . '</Input>
                        <Input name="ownerIdType">' . $post['ownerIdType'] . '</Input>
                        <Input name="ownerName">' . $post['ownerName'] . '</Input>
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
