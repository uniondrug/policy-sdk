<?php

namespace Uniondrug\PolicySdk\Sdks\RenBaoCar\Modules;

trait CollectInfo{
    public function collectInfo(array $post){
        $request_content = '<Request> 
              <InputsList> 
                <Inputs type="applicantInfo"> 
                  <Input name="applicantName">' . $post['applicantName'] . '</Input>  
                  <Input name="applicantIdType">' . $post['applicantIdType'] . '</Input>  
                  <Input name="applicantIdNo">' . $post['applicantIdNo'] . '</Input>  
                  <Input name="applicantMobile">' . $post['applicantMobile'] . '</Input>  
                  <Input name="applicantNation">' . $post['applicantNation'] . '</Input>  
                  <Input name="applicantAddress">' . $post['applicantAddress'] . '</Input>  
                  <Input name="applicantIssuer">' . $post['applicantIssuer'] . '</Input>  
                  <Input name="applicantCertiStartDat">' . $post['applicantCertiStartDat'] . '</Input>  
                  <Input name="applicantCertiEndDate">' . $post['applicantCertiEndDate'] . '</Input> 
                </Inputs>  
                <Inputs type="insuredInfo"> 
                  <Input name="insuredName">' . $post['insuredName'] . '</Input>  
                  <Input name="insuredIdType">' . $post['insuredIdType'] . '</Input>  
                  <Input name="insuredIdNo">' . $post['insuredIdNo'] . '</Input>  
                  <Input name="insuredMobile">' . $post['insuredMobile'] . '</Input>  
                  <Input name="insuredNation">' . $post['insuredNation'] . '</Input>  
                  <Input name="insuredAddress">' . $post['insuredAddress'] . '</Input>  
                  <Input name="insuredIssuer">' . $post['insuredIssuer'] . '</Input>  
                  <Input name="insuredCertiStartDat">' . $post['insuredCertiStartDat'] . '</Input>  
                  <Input name="insuredCertiEndDate">' . $post['insuredCertiEndDate'] . '</Input> 
                </Inputs> 
              </InputsList> 
            </Request>';
        try {
            $resultArray = $this->getCurl($request_content,__FUNCTION__,$post['transactionNo'],'315');
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
