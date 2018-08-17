<?php

namespace Uniondrug\PolicySdk\Sdks\RenBaoXml\Modules;

trait Surrender
{
    public function surrender(array $post)
    {
        $waterNo = $this->createUniqueWaterNo();
        $Md5Value = md5($waterNo . $post['policyNo']);
        $xml_content = '<?xml version="1.0" encoding="GB2312" standalone="yes"?>
            <PolicyEndorsement>
                <Head>
                    <UUID>' . $waterNo . '</UUID>
                    <PlateformCode>CPI000465</PlateformCode>
                    <Md5Value>' . $Md5Value . '</Md5Value>
                </Head>
                <EndorseInfos>
                    <EndorseInfo>
                        <PolicyNo>' . $post['policyNo'] . '</PolicyNo>
                        <EndorseType>00</EndorseType>
                        <EndorseDate>' . date('Y-m-d H:i:s') . '</EndorseDate>
                    </EndorseInfo>
                </EndorseInfos>
             </PolicyEndorsement>';
        $postData = [
            'interfaceNo' => '001003',
            'datas' => convert_encoding($xml_content, 'GB2312')
        ];
        $url = $this->config->surrender;
        $client = new \nusoap_client($url, 'wsdl');
        $client->soap_defencoding = 'utf-8';
        $client->decode_utf8 = false;
        $client->xml_encoding = 'utf-8';
        $this->logger->surrender()->info("保司请求报文:" . $xml_content);
        $result = $client->call('modifyService', $postData);
        $resultXml = $result['return'];
        $this->logger->surrender()->info("保司响应报文:" . $resultXml);
        if ($err = $client->getError()) {
            return $this->withError($err);
        }
        $resultObj = xml_to_array($resultXml, 'GB2312');
        if ($resultObj['Head']['ErrorCode'] != '00') {
            return $this->withError($resultObj['Head']['ErrorMessage']);
        }
        $returnObj =  $resultObj['EndorseInfoReturns']['EndorseInfoReturn'];
        if ($returnObj['ResponseCode'] != '00') {
            return $this->withError($returnObj['ResponseMessage']);
        }
        $data = [
            'policyNo' => $post['policyNo'],
            'transTime' => date("Y-m-d H:i:s")
        ];
        return $this->withData($data);
    }
}