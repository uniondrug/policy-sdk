<?php

namespace Uniondrug\PolicySdk\Sdks\RenBaoCar\Modules;

trait DefaultQuote {
    public function defaultQuote(array $post){
        $request_content = '<Request>
                    <InputsList>
                        <Inputs type="insuredInfo">
                            <Input name="insuredIdNo">' . $post['insuredIdNo'] . '</Input>
                            <Input name="insuredIdType">' . $post['insuredIdType'] . '</Input>
                            <Input name="insuredGender">' . $post['insuredGender'] . '</Input>
                            <Input name="insuredName">' . $post['insuredName'] . '</Input>
                            <Input name="insuredBirthday">' . $post['insuredBirthday'] . '</Input>
                        </Inputs>
                        <Inputs type="vehicleInfo">
                            <Input name="forceBeginDate">' . $post['forceBeginDate'] . '</Input>
                            <Input name=" subMonopolyType">' . $post['subMonopolyType'] .'</Input>
                            <Input name="vehicleSeats">' . $post['vehicleSeats'] . '</Input>
                            <Input name=" monopolyname">' . $post['monopolyname'] .'</Input>
                            <Input name="bizBeginDate">' . $post['bizBeginDate'] . '</Input>
                            <Input name=" monopolycode">' . $post['monopolycode'] .'</Input>
                            <Input name="loanName">' . $post['loanName'] . '</Input>
                            <Input name="loanFlag">' . $post['loanFlag'] . '</Input>
                            <Input name="bizBeginDateHour">' . $post['bizBeginDateHour'] . '</Input>
                            <Input name="vehicleType">' . $post['vehicleType'] . '</Input>
                            <Input name="traveltaxAddress">' . $post['traveltaxAddress'] . '</Input>
                            <Input name="forceBeginDateHour">' . $post['forceBeginDateHour'] . '</Input>
                            <Input name="checkCode">' . $post['checkCode'] . '</Input>
                            <Input name="checkCodeCI">' . $post['checkCodeCI'] . '</Input>
                            <Input name="carproofdate">' . $post['carproofdate'] . '</Input>
                            <Input name="fueltype">' . $post['fueltype'] . '</Input>
                            <Input name="carprooftype">' . $post['carprooftype'] . '</Input>
                            <Input name="carproofno">' . $post['carproofno'] . '</Input>
                        </Inputs>
                    </InputsList> 
                </Request>';
        try {
            $resultArray = $this->getCurl($request_content,__FUNCTION__,$post['transactionNo'],'101106');
        } catch (\Exception $e) {
            return $this->withError($e->getMessage());
        }
        //100 成功   600 商业险平台验证码  700 交强险平台验证码
        if ($resultArray['Package']['Header']['Status'] != 100 && $resultArray['Package']['Header']['Status'] != 600 && $resultArray['Package']['Header']['Status'] != 700) {
            return $this->withError($resultArray['Package']['Header']['ErrorMessage'],$resultArray['Package']['Header']['Status']);
        }
        $data = [
            'header' => $resultArray['Package']['Header'],
            'data' => $resultArray['data'],
        ];
        return $this->withData($data);
    }
}
