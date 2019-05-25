<?php

namespace Uniondrug\PolicySdk\Sdks\RenBaoCar\Modules;

trait CheckQuote{
    public function checkQuote (array $post){
        $request_content = '<Request>
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
                </Request>';
        try {
            $resultArray = $this->getCurl($request_content,__FUNCTION__,$post['transactionNo'],'101100');
        } catch (\Exception $e) {
            return $this->withError($e->getMessage());
        }
    }
}
