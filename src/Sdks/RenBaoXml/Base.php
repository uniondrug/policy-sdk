<?php

namespace Uniondrug\PolicySdk\Sdks\RenBaoXml;

use Uniondrug\PolicySdk\Sdk;
use Uniondrug\PolicySdk\Sdks\RenBaoXml\Modules\Insure;
use Uniondrug\PolicySdk\Sdks\RenBaoXml\Modules\Surrender;

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

    /*
     * 退保
     */
    use Surrender;

    public function createUniqueWaterNo()
    {
        return parent::createUniqueWaterNo(self::water_no_length);
    }
}