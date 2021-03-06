<?php

namespace Uniondrug\PolicySdk\Sdks\AiBao\Modules;

trait PolicyQuery
{
    public function policyQuery(array $post)
    {
        $postData = [
            'head' => $this->getHeader($post),
            'body' => [
                "mainInfo" => [
                    "orderNo" => $post['outTradeNo'],
                    "channelId" => $this->config->channelId,
                    "licenseNo" => $post['licenseNo'],
                ]
            ],
        ];
        $postJson = json_encode($postData, JSON_UNESCAPED_UNICODE);
        $this->logger->policyQuery()->info("保司请求报文:" . $postJson);
        $header = ['Content-Type: application/json'];
        $url = $this->setUrl('100074');
        try {
            $result = $this->curl_https($url, $postJson, $header, __FUNCTION__,'120');
        } catch (\Exception $e) {
            return $this->withError($e->getMessage());
        }
        $this->logger->policyQuery()->info("保司响应报文:" . $result);
        $resultObj = json_decode($result,true);
        return $this->returnRes($resultObj);
    }
}