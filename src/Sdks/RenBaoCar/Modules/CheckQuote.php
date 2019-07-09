<?php

namespace Uniondrug\PolicySdk\Sdks\RenBaoCar\Modules;

trait CheckQuote
{
    public function checkQuote(array $post)
    {
        $request_content = '<Request>
                    <InputsList>
                        <Inputs type="vehicleInfo">
                            <Input name="cityCode">' . $post['cityCode'] . '</Input>
                            <Input name="noLicenseFlag">' . $post['noLicenseFlag'] . '</Input>
                            <Input name="licenseNo">' . $post['licenseNo'] . '</Input>
                            <Input name="commerialVehicleFlag">'.$post['commerialVehicleFlag'].'</Input>
                        </Inputs>
                        <Inputs type="belongConInfo">
                            <Input name="isCooperSaveInfo">'.$post['isCooperSaveInfo'].'</Input>
                            <Input name="handler1code">'.$post['handler1code'].'</Input>
                            <Input name="handlercode">'.$post['handlercode'].'</Input>
                            <Input name="sendfixcode">'.$post['sendfixcode'].'</Input>
                            <Input name="makecode">'.$post['makecode'].'</Input>
                            <Input name="comcode">'.$post['comcode'].'</Input>
                            <Input name="operatorcode">'.$post['operatorcode'].'</Input>
                        </Inputs>
                    </InputsList>
                </Request>';
        try {
            $resultArray = $this->getCurl($request_content, __FUNCTION__, $post['transactionNo'], '101100');
        } catch (\Exception $e) {
            return $this->withError($e->getMessage());
        }
        if ($resultArray['Package']['Header']['Status'] != 100) {
            return $this->withError($resultArray['Package']['Header']['ErrorMessage'], $resultArray['Package']['Header']['Status']);
        }
        $data = [
            'header' => $resultArray['Package']['Header'],
            'data' => $resultArray['Package']['Response'],
        ];
        return $this->withData($data);
    }
}
