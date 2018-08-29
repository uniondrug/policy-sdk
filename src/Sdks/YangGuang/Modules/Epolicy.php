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
        $xml = '<?xml version="1.0" encoding="GBK"?>
        <INSURENCEINFO>
          <USERNAME>' . $this->config->user . '</USERNAME>
          <PASSWORD>' . $this->config->password . '</PASSWORD>
          <POLICYNO>'. $post['policyNo'] .'</POLICYNO>
        </INSURENCEINFO>';
        $xml_content = convert_encoding($xml, 'gbk');
        $postData = array(
            'data' => $xml_content,
            'sign' => md5($this->config->token . $xml_content),
            'functionFlag' => 'EPOLICY',
            'interfaceFlag' => 'TCYG',
        );
        $this->logger->epolicy()->info("保司请求报文:" . convert_encoding($xml));
        $header = ['Content-Type: application/x-www-form-urlencoded'];;
        $postQuery = http_build_query($postData);
        try {
            $result = $this->curl_https($this->config->insure, $postQuery, $header, __FUNCTION__);
        } catch (\Exception $e) {
            return $this->withError($e->getMessage());
        }
        $this->logger->epolicy()->info("保司响应报文:" . convert_encoding($result));
        $resultObj = xml_to_array($result);
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