<?php

namespace Uniondrug\PolicySdk\Sdks\RenBaoCar;

use Uniondrug\PolicySdk\Sdk;
use Uniondrug\PolicySdk\Sdks\RenBaoCar\Modules\CheckQuote;
use Uniondrug\PolicySdk\Sdks\RenBaoCar\Modules\CollectInfo;
use Uniondrug\PolicySdk\Sdks\RenBaoCar\Modules\DefaultQuote;
use Uniondrug\PolicySdk\Sdks\RenBaoCar\Modules\FreeQuote;
use Uniondrug\PolicySdk\Sdks\RenBaoCar\Modules\PolicyReading;
use Uniondrug\PolicySdk\Sdks\RenBaoCar\Modules\SeatFollow;
use Uniondrug\PolicySdk\Sdks\RenBaoCar\Modules\SuppleQuote;
use Uniondrug\PolicySdk\Sdks\RenBaoCar\Modules\Verify;

class Base extends Sdk
{
    /*
    * 人保接口只能支持最大长度32位
    */
    const water_no_length = 32;

    /*
     * 投保
     */
    use CheckQuote;

    /*
     * 收集信息
     */
    use CollectInfo;

    /*
     * 报价
     */
    use DefaultQuote;

    /*
     * 自由报价
     */
    use FreeQuote;

    /*
     * 电子阅读
     */
    use PolicyReading;

    /*
     * 坐席跟进
     */
    use SeatFollow;

    /*
     * 补充报价
     */
    use SuppleQuote;

    /*
     * 验证码
     */
    use Verify;


    /**
     * 创建唯一流水号
     */
    public function createUniqueWaterNo()
    {
        return parent::createUniqueWaterNo(self::water_no_length);
    }

    /**
     * 请求人保接口
     * @param $xml_content
     * @return array|mixed|\SimpleXMLElement
     */
    public function getCurl($xml_content){
        $url = $this->config->renbaoUrl;
        $postJson = json_encode($xml_content, JSON_UNESCAPED_UNICODE);
        $header = ['Content-Type: application/soap+xml'];
        try {
            $result = $this->curl_https($url, $postJson, $header, __FUNCTION__,'120');
        } catch (\Exception $e) {
            return $this->withError($e->getMessage());
        }
        $resultObj = xml_to_array($result, 'GB2312');
        if( $resultObj['Package']['Header']['Status'] != 100 ){
            return $this->withError($resultObj['Package']['Header']['ErrorMessage']);
        }
        return $resultObj;
    }

}