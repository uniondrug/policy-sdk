<?php

namespace Uniondrug\PolicySdk\Sdks\HuaXia;

use Uniondrug\PolicySdk\Sdk;
use Uniondrug\PolicySdk\Sdks\HuaXia\Modules\EquityClaim;
use Uniondrug\PolicySdk\Sdks\HuaXia\Modules\Insure;
use Uniondrug\PolicySdk\Sdks\HuaXia\Modules\Surrender;

class Base extends Sdk
{
    /*
     * 华夏权益理赔接口只能支持最大长度20位
     */
    const water_no_length = 20;

    /*
     * 投保
     */
    use Insure;

    /*
     * 退保
     */
    use Surrender;

    /*
     * 权益理赔
     */
    use EquityClaim;

    protected function createParams($postData)
    {
        $params = [
            'uid' => 'adpt_tongcheng',
            'timestamp' => str_replace(".", "", microtime(1)),
            'nonce' => substr(md5(microtime(1)), 0, 20),
            'data' => json_encode($postData, JSON_UNESCAPED_UNICODE)
        ];
        $params['signature'] = md5($this->getLinkString($params) . $this->config->key);
        return $params;
    }

    protected function getLinkString($params)
    {
        $params = array_filter($params); // 过滤空值
        unset($params['signature']); // 去掉签名,如有
        ksort($params);
        $peers = array();
        foreach ($params as $k => $v) {
            $peers[] = "$k=$v";
        }
        return implode("&", $peers);
    }

    public function createUniqueWaterNo()
    {
        return parent::createUniqueWaterNo(self::water_no_length);
    }
}