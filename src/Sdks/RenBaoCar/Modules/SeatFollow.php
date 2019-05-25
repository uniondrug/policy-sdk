<?php

namespace Uniondrug\PolicySdk\Sdks\RenBaoCar\Modules;

trait SeatFollow{
    public function seatFollow(array $post){
        $request_content = '<Request> 
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
            </Request>';
        try {
            $resultArray = $this->getCurl($request_content,__FUNCTION__,$post['transactionNo'],'400');
        } catch (\Exception $e) {
            return $this->withError($e->getMessage());
        }
        if ($resultArray['Package']['Header']['Status'] != 100) {
            return $this->withError($resultArray['Package']['Header']['ErrorMessage']);
        }
        $data = [
            'header' => $resultArray['Package']['Header'],
            'data' => $resultArray['Package']['Response'],
        ];
        return $this->withData($data);
    }
}
