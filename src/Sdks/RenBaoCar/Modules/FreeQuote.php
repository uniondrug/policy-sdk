<?php

namespace Uniondrug\PolicySdk\Sdks\RenBaoCar\Modules;

trait FreeQuote{
    public function freeQuote(array $post){
        $optional = '';
        foreach ($post['optionInfo'] as $key => $val){
            $optional .= '<Input name="'.$val["kindCode"].'">'.$val['kindValue'].'</Input>';
        }
        $request_content = '
                <Request>
                    <InputsList>
                        <Inputs type="optional">
                            <Input name="bizFlag">'.$post['bizFlag'].'</Input>
                            <Input name="forceFlag">'.$post['forceFlag'].'</Input>
                            '.$optional.'
                        </Inputs>
                        <Inputs type="vehicleInfo">
                            <Input name="checkCode">'.$post['checkCode'].'</Input>
                            <Input name="checkCodeCI">'.$post['checkCodeCI'].'</Input>
                            <Input name="bizBeginDate">'.$post['bizBeginDate'].'</Input>
                            <Input name="bizBeginDateHour">'.$post['bizBeginDateHour'].'</Input>
                            <Input name="forceBeginDate">'.$post['forceBeginDate'].'</Input>
                            <Input name="forceBeginDateHour">'.$post['forceBeginDateHour'].'</Input>
                            <Input name="carShipFlag">'.$post['carShipFlag'].'</Input>
                        </Inputs>
                    </InputsList>
                </Request>';
        try {
            $resultArray = $this->getCurl($request_content,__FUNCTION__,$post['TransactionNo'],'101110');
        } catch (\Exception $e) {
            return $this->withError($e->getMessage());
        }
        if ($resultArray['Package']['Header']['Status'] != 100 && $resultArray['Package']['Header']['Status'] != 600 && $resultArray['Package']['Header']['Status'] != 700) {
            return $this->withError("报错信息:".$resultArray['Package']['Header']['ErrorMessage']." Status:".$resultArray['Package']['Header']['Status']);
        }
        $data = [
            'header' => $resultArray['Package']['Header'],
            'data' => $resultArray['data'],
        ];
        return $this->withData($data);
    }
}
