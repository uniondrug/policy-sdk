<?php

namespace Uniondrug\PolicySdk\Sdks\RenBaoCar\Modules;

trait PolicyReading{
    public function poliReading(array $post){
        $request_content = '<request>
                <ClickEntryTime>' . $post['ClickEntryTime'] . '</ClickEntryTime>
            </request>';
        try {
            $resultArray = $this->getCurl($request_content,__FUNCTION__,$post['transactionNo'],'330');
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
