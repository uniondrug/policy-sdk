<?php

namespace Uniondrug\PolicySdk\Sdks\RenBaoCar\Modules;

trait PolicyReading{
    public function poliReading(array $post){
        $xml_content = '<?xml version="1.0" encoding="utf-8"?>
        <Packet>
            <Header>
                <RequestType>330</RequestType>
                <Version>2</Version>
                <InsureType>100</InsureType>
                <SessionId>' . $post['transactionNo'] . '</SessionId>
                <SellerId>123456</SellerId>
                <From>PICCTEST</From>
                <SendTime>' . date('Y-m-d H:i:s',time()) . '</SendTime>
                <Status>100</Status>
                <ErrorMessage></ErrorMessage>
            </Header>
            <request>
                <ClickEntryTime>' . $post['ClickEntryTime'] . '</ClickEntryTime>
            </request>
        </Packet>';
        $resultObj = $this->getCurl($xml_content);
        $data = [
            'header' => $resultObj['Package']['Header'],
            'data' => $resultObj['Package']['Response'],
        ];
        return $this->withData($data);
    }
}
