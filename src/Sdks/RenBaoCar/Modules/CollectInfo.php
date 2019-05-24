<?php

namespace Uniondrug\PolicySdk\Sdks\RenBaoCar\Modules;

trait CollectInfo{
    public function collectInfo(array $post){
        $xml_content = '<?xml version="1.0" encoding="utf-8"?>
        <PackageList> 
          <Package> 
            <Header> 
              <RequestType>315</RequestType>  
              <Version>2</Version>  
              <InsureType>100</InsureType>  
              <SessionId>' . $post['transactionNo'] . '</SessionId>  
              <SellerId>123456</SellerId>  
              <From>DDINSURE</From>  
              <SendTime>' . date('Y-m-d H:i:s',time()) . '</SendTime>  
              <Status>100</Status>  
              <ErrorMessage/> 
            </Header>  
            <Request> 
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
