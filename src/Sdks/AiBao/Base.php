<?php

namespace Uniondrug\PolicySdk\Sdks\AiBao;

use Uniondrug\PolicySdk\Sdk;
use Uniondrug\PolicySdk\Sdks\AiBao\Modules\CarQuery;
use Uniondrug\PolicySdk\Sdks\AiBao\Modules\ClickQuery;
use Uniondrug\PolicySdk\Sdks\AiBao\Modules\DefaultQuote;
use Uniondrug\PolicySdk\Sdks\AiBao\Modules\FreeQuote;
use Uniondrug\PolicySdk\Sdks\AiBao\Modules\IdCheck;
use Uniondrug\PolicySdk\Sdks\AiBao\Modules\InsureCheck;
use Uniondrug\PolicySdk\Sdks\AiBao\Modules\OrderCheck;
use Uniondrug\PolicySdk\Sdks\AiBao\Modules\PolicyQuery;

class Base extends Sdk
{
    const water_no_length = 15;
    /*
     * 承保检查（接口编号：100069）
     */
    use InsureCheck;
    /*
     * 车型查询（接口编号：100070）
     */
    use CarQuery;
    /*
     * 默认报价（接口编号：100071）
     */
    use DefaultQuote;
    /*
     * 自由报价（接口编号：100072）
     */
    use FreeQuote;
    /*
     * 提交核保（接口编号：100073）
     */
    use OrderCheck;
    /*
     * 保单状态查询（接口编号：100074）
     */
    use PolicyQuery;
    /*
     * 平台投保验证码身份信息采集（接口编号：100080）
     */
    use IdCheck;
    /*
     * 电子投保链接点击状态查询（接口编号：100081）
     */
    use ClickQuery;
    //组装请求地址
    public function setUrl($interface){
        $url_param = [
            'system' => $this->config->operator, //系统调用方系统编号,爱保科技进行配置
            'interface' => $interface, //当前所请求的接口编号
            'mode' => $this->config->mode
        ];
        return $this->config->url.'?param='.base64_encode(json_encode($url_param));
    }

    //生成流水号
    public function createUniqueWaterNo()
    {
        return parent::createUniqueWaterNo(self::water_no_length);
    }

    /**
     * 证件类型转换
     * @param $identityType
     * @return int
     */
    protected function convertIdentifyType($identityType)
    {
        switch ($identityType) {
            //  身份证
            case "01":
                $type = "01";
                break;
            //  军官证
            case "02":
                $type = "04";
                break;
            //  护照
            case "03":
                $type = "03";
                break;
            //  港澳通行证
            case "04":
                $type = "25";
                break;
            //  台湾通行证
            case "05":
                $type = "26";
                break;
            //  驾驶证
            case "06":
                $type = "05";
                break;
            default:
                $type = "99";
                break;
        }
        return $type;
    }

    public function returnRes($resultObj){
        if($resultObj['head']['errorCode'] != '0000'){
            return $this->withError($resultObj['head']['errorMsg'],$resultObj['head']['errorCode']);
        }
        return $this->withData($resultObj);
    }
}