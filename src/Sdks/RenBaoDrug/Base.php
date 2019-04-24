<?php

namespace Uniondrug\PolicySdk\Sdks\RenBaoDrug;

use Uniondrug\PolicySdk\Sdk;
use Uniondrug\PolicySdk\Sdks\RenBaoDrug\Modules\Insure;
class Base extends Sdk
{
    /*
    * 人保接口只能支持最大长度32位
    */
    const water_no_length = 32;

    /*
     * 投保
     */
    use Insure;


    public function createUniqueWaterNo()
    {
        return parent::createUniqueWaterNo(self::water_no_length);
    }
}