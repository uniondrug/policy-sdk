<?php

namespace Uniondrug\PolicySdk\Sdks\YangGuang;

use Uniondrug\PolicySdk\Sdk;
use Uniondrug\PolicySdk\sdks\YangGuang\Modules\Epolicy;
use Uniondrug\PolicySdk\sdks\YangGuang\Modules\Insure;
use Uniondrug\PolicySdk\sdks\YangGuang\Modules\Surrender;

class Base extends Sdk
{
    public function __construct($name)
    {
        parent::__construct($name);
    }

    /*
     *  投保
     */
    use Insure;

    /*
     * 退保
     */
    use Surrender;

    /*
     * 电子保单
     */
    use Epolicy;

    protected function xml_to_array($xml)
    {
        $dataObj = simplexml_load_string($xml);
        $dataObj = json_decode(str_replace("{}",'""',json_encode((array)$dataObj)),true);
        return $dataObj;
    }
}