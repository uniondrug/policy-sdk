<?php

namespace Uniondrug\PolicySdk\Sdks\AiBao\Modules;

trait ClickQuery
{
    public function clickQuery(array $post)
    {
        $postData = [
            'head' => $this->getHeader($post),
            'body' => [],
        ];
        $postJson = json_encode($postData, JSON_UNESCAPED_UNICODE);
        $this->logger->clickQuery()->info("保司请求报文:" . $postJson);
        $header = ['Content-Type: application/json'];
        $url = $this->setUrl('100081');
        try {
            $result = $this->curl_https($url, $postJson, $header, __FUNCTION__,'120');
        } catch (\Exception $e) {
            return $this->withError($e->getMessage());
        }
        $this->logger->clickQuery()->info("保司响应报文:" . $result);
        $resultObj = json_decode($result,true);
        return $this->returnRes($resultObj);
    }
}