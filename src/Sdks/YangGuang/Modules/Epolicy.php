<?php

namespace Uniondrug\PolicySdk\Sdks\YangGuang\Modules;

trait Epolicy
{
    /**
     * 获取电子保单地址
     * @param array $post
     */
    public function epolicy(array $post)
    {
        $xml_content = '<?xml version="1.0" encoding="utf-8"?>
        <INSURENCEINFO>
          <USERNAME>AA8BB6106C85451C4DF5F09EAB9EBC5D</USERNAME>
          <PASSWORD>1F6B1264ABFC025BE1F20946168B5438</PASSWORD>
          <POLICYNO>'. $post['policyNo'] .'</POLICYNO>
        </INSURENCEINFO>';
        $xml_content = iconv('utf-8', 'gbk', $xml_content);
        $postData = array(
            'data' => $xml_content,
            'sign' => md5($this->config->token . $xml_content),
            'functionFlag' => 'EPOLICY',
            'interfaceFlag' => 'TCYG',
        );
        $this->logger->epolicy()->info("保司请求报文:" . $xml_content);
        $header = ['Content-Type: application/x-www-form-urlencoded'];;
        $postQuery = http_build_query($postData);
        try {
            $result = $this->curl_https($this->config->insure, $postQuery, $header, __FUNCTION__);
        } catch (\Exception $e) {
            return $this->withError($e->getMessage());
        }
        $this->logger->epolicy()->info("保司响应报文:" . iconv("GBK", "UTF-8", $result));
        $resultObj = $this->xml_to_array($result);
        $policyObj = $resultObj['POLICY'];
        if ($policyObj['@attributes']['RETURN'] != "true") {
            return $this->withError($policyObj['ERROR']['@attributes']['INFO']);
        }
        $data = [
            'epolicyAddress' => urlencode(urldecode($policyObj['EFORM']['@attributes']['URL'])),
        ];
        return $this->withData($data);
    }
}