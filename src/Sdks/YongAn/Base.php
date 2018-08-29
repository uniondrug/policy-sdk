<?php

namespace Uniondrug\PolicySdk\Sdks\YongAn;

use Uniondrug\PolicySdk\Sdk;
use Uniondrug\PolicySdk\Sdks\YongAn\Modules\Insure;
use Uniondrug\PolicySdk\Sdks\YongAn\Modules\Surrender;

class Base extends Sdk
{
    /*
     * 永安接口只能支持最大长度23位
     */
    const water_no_length = 23;

    /*
     * 投保
     */
    use Insure;

    /*
     * 退保
     */
    use Surrender;

    protected function ISODateString(string $date = "")
    {
        $date = $date ? strtotime($date) : time();
        return date('Y-m-d\TH:i:s+08:00', $date);
    }


    public function createUniqueWaterNo()
    {
        return parent::createUniqueWaterNo(self::water_no_length);
    }
}