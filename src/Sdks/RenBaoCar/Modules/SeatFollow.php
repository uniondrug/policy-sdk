<?php

namespace Uniondrug\PolicySdk\Sdks\RenBaoCar\Modules;

trait SeatFollow{
    public function seatFollow(array $post){
        $xml_content = '<?xml version="1.0" encoding="utf-8"?>
        <?xml version="1.0" encoding="utf-8"?>
        <PackageList> 
          <Package> 
            <Header> 
              <RequestType>400</RequestType>  
              <Version>2</Version>  
              <InsureType>100</InsureType>  
              <SessionId>' . $post['transactionNo'] . '</SessionId>  
              <SellerId>123456</SellerId>  
              <From>YC</From>  
              <SendTime>' . date('Y-m-d H:i:s',time()) . '</SendTime>  
              <Status>100</Status>  
              <ErrorMessage/> 
            </Header>  
                <Request> 
                    <sessionId>' . $post['sessionId'] . '</sessionId> 
                    <leadsId>' . $post['leadsId'] . '</leadsId>
                    <operationType>' . $post['operationType'] . '</operationType>
                    <cancelFlag>1</cancelFlag>
                    <realMobile>' . $post['realMobile'] . '</realMobile> 
                    <virtualMobile>' . $post['virtualMobile'] . '</virtualMobile> 
                    <isQuote>' . $post['isQuote'] . '</isQuote> 
                    <vehicleInfo>
                        <citycode>' . $post['citycode'] . '</citycode>
                        <licenseNo>' . $post['licenseNo'] . '</licenseNo>
                        <commerialVehicleFlag>' . $post['commerialVehicleFlag'] . '</commerialVehicleFlag>
                        <vehicleFrameNo>' . $post['vehicleFrameNo'] . '</vehicleFrameNo>
                        <engineNo>' . $post['engineNo'] . '</engineNo>
                        <registerDate>' . $post['registerDate'] . '</registerDate>
                        <policyEndDate>' . $post['policyEndDate'] . '</policyEndDate>
                        <vehicleBrand>' . $post['vehicleBrand'] . '</vehicleBrand>
                        <isProposalPicc>' . $post['isProposalPicc'] . '</isProposalPicc>
                    </vehicleInfo>
                    <ownerInfo>
                        <ownerName>' . $post['ownerName'] . '</ownerName>
                        <ownerIdType>' . $post['ownerIdType'] . '</ownerIdType>
                        <ownerIdNo>' . $post['ownerIdNo'] . '</ownerIdNo>
                    </ownerInfo>
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
