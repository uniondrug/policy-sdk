<?php

namespace Uniondrug\PolicySdk\Sdks\RenBaoCar\Modules;

trait Verify{
    public function verify(array $post){
        $request_content = '<Request>
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
            </Request>';
        try {
            $resultArray = $this->getCurl($request_content,__FUNCTION__,$post['transactionNo'],'100');
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
