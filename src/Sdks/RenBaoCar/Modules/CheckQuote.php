<?php

namespace Uniondrug\PolicySdk\Sdks\RenBaoCar\Modules;

trait CheckQuote{
    public function checkQuote (array $post){
        $xml_content = '<?xml version="1.0" encoding="utf-8"?>
        <PackageList>
            <Package>
                <Header>
                    <Version>2</Version>
                    <RequestType>101100</RequestType>
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
                        <Inputs type="vehicleInfo">
                            <Input name="cityCode">' . $post['cityCode'] . '</Input>
                            <Input name="noLicenseFlag">' . $post['cityCode'] . '</Input>
                            <Input name="licenseNo">' . $post['licenseNo'] . '</Input>
                            <Input name="commerialVehicleFlag">1</Input>
                        </Inputs>
                        <Inputs type="belongConInfo">
                            <Input name="isCooperSaveInfo">0</Input>
                            <Input name="handler1code">230100</Input>
                            <Input name="handlercode">230100</Input>
                            <Input name="sendfixcode">230100</Input>
                            <Input name="makecode">230100</Input>
                            <Input name="comcode">230100</Input>
                            <Input name="operatorcode">230100</Input>
                        </Inputs>
                    </InputsList>
                </Request>		
                <Sign>WJlEdDU3m</Sign>
            </Package>
        </PackageList>';
        $resultObj = $this->getCurl($xml_content);
        $data = [
            'header' => $resultObj['Package']['Header'],
            'data' => $resultObj['Package']['Response'],
        ];
        return $this->withData($data);


        $returnData = '<?xml version="1.0" encoding="GBK" standalone="yes"?>
<PackageList>
	<Package>
		<Header>
			<Version>2</Version>
			<RequestType>101100</RequestType>
			<InsureType>100</InsureType>
			<SessionId>9a54b5df-e99a-4d25-842e-0aaf6aa530e8</SessionId>
			<SendTime>2016-05-18 17:14:05</SendTime>
			<Status>100</Status>
			<ErrorMessage>成功</ErrorMessage>
		</Header>
		<Response>
		</Response>
		<Sign>YuiXkErCFf9eUltevd-bkfyvo6OZhJoIdcM3mAMg2v8izxXKPvGBQWaj5U_46sJdM6YGXTxpeGMIYs9e8I7Bve7YnpY6yKnFEu1rdh6XI24jrUtxEWQ10Ms_py8kdl1O5HPfipf3A2PaqUaIKcObPQqCUiuhtI3t_Q8emVXqVw4</Sign>
	</Package>
</PackageList>';
        $resultObj = xml_to_array($returnData, 'GB2312');
        if( $resultObj['Package']['Header']['Status'] != 100 ){
            return $this->withError($resultObj['Package']['Header']['ErrorMessage']);
        }
        $data = [
            'header' => $resultObj['Package']['Header'],
            'data' => $resultObj['Package']['Response'],
        ];
        return $this->withData(json_encode($data));
    }
}
