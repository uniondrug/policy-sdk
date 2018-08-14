<?php

namespace Uniondrug\PolicySdk\Sdks\YangGuang\Modules;

trait Surrender
{
    public function surrender(array $post)
    {
        $xml_content = '<?xml version="1.0" encoding="utf-8"?>
        <INSURENCEINFO>
          <USERNAME>' . $this->config->user . '</USERNAME>
          <PASSWORD>' . $this->config->password . '</PASSWORD>
          <POLICYNO>'. $post['policyNo'] .'</POLICYNO>
        </INSURENCEINFO>';
        $xml_content = iconv('utf-8', 'gbk', $xml_content);
        $postData = array(
            'data' => $xml_content,
            'sign' => md5($this->config->token . $xml_content),
            'functionFlag' => 'SURRENDER',
            'interfaceFlag' => 'TCYG',
        );
        $this->logger->surrender()->info("保司请求报文:" . $xml_content);
        $header = ['Content-Type: application/x-www-form-urlencoded'];;
        $postQuery = http_build_query($postData);
        try {
            $result = $this->curl_https($this->config->insure, $postQuery, $header, __FUNCTION__);
        } catch (\Exception $e) {
            return $this->withError($e->getMessage());
        }
        $this->logger->surrender()->info("保司响应报文:" . iconv("GBK", "UTF-8", $result));
        $resultObj = $this->xml_to_array($result);
        $orderObj = $resultObj['ORDER'];
        $policyObj = $orderObj['POLICY'];
        if ($orderObj['@attributes']['RETURN'] != 'true') {
            $error = $orderObj['@attributes']['ERROR'] ?: "";
            $error = !$error ? $policyObj['ERROR']['@attributes']['INFO'] : "未知异常,请联系管理员";
            return $this->withError($error);
        }
        $data = [
            'policyNo' => $post['policyNo'],
            'transTime'=> date("Y-m-d H:i:s")
        ];
        return $this->withData($data);
    }
}