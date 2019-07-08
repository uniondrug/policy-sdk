<?php
namespace Uniondrug\PolicySdk\Sdks\RenBaoCar\Modules;

trait SuppleQuote{
    public function suppleQuote (array $post) {
        $request_content = '<Request>
                <InputsList>
                    <Inputs type="vehicleInfo">
                        <Input name="vehicleBrand">' . $post['vehicleBrand'] . '</Input>
                        <Input name="vehicleFrameNo">' . $post['vehicleFrameNo'] . '</Input>
                        <Input name="engineNo">' . $post['engineNo'] . '</Input>
                        <Input name="specialCarFlag">' . $post['specialCarFlag'] . '</Input>
                        <Input name="registerDate">' . $post['registerDate'] . '</Input>
                        <Input name="checkCodeJS">' . $post['checkCodeJS'] . '</Input>
                        <Input name="vehicleModel">' . $post['vehicleModel'] . '</Input>
                        <Input name="specialCarDate">' . $post['specialCarDate'] . '</Input>
                        <Input name="buyCarDate">' . $post['buyCarDate'] . '</Input>
                    </Inputs>
                    <Inputs type="ownerInfo">
                        <Input name="ownerMobile">' . $post['ownerMobile'] . '</Input>
                        <Input name="ownerIdNo">' . $post['ownerIdNo'] . '</Input>
                        <Input name="ownerIdType">' . $post['ownerIdType'] . '</Input>
                        <Input name="ownerName">' . $post['ownerName'] . '</Input>
                        <Input name="ownerGender">' . $post['ownerGender'] . '</Input>
                        <Input name="ownerBirthday">' . $post['ownerBirthday'] . '</Input>
                    </Inputs>
                </InputsList>
            </Request>';
        try {
            $resultArray = $this->getCurl($request_content,__FUNCTION__,$post['TransactionNo'],'101105');
        } catch (\Exception $e) {
            return $this->withError($e->getMessage());
        }
        if ($resultArray['Package']['Header']['Status'] != 200) {
            return $this->withError($resultArray['Package']['Header']['ErrorMessage']);
        }
        $data = [
            'header' => $resultArray['Package']['Header'],
            'data' => $resultArray['Package']['Response'],
            'tags' => $resultArray['data']
        ];
        return $this->withData($data);
    }
}
