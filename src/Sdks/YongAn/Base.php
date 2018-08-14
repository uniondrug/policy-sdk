<?php

namespace Uniondrug\PolicySdk\Sdks\YongAn;

use Uniondrug\PolicySdk\Sdk;
use Uniondrug\PolicySdk\Sdks\YongAn\Modules\Insure;
use Uniondrug\PolicySdk\Sdks\YongAn\Modules\Surrender;

class Base extends Sdk
{
    public function __construct($name)
    {
        parent::__construct($name);
    }

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
        return date('Y-m-d\TH:i:s+08:00',$date);
    }

    /*
     * 永安接口只能支持最大长度26位
     */
    protected function createUniqueWaterNo()
    {
        list($usec, $sec) = explode(" ", microtime());
        $msec = round($usec * 1000);
        $waterNo = date('ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8) . rand(100000, 999999);
        $waterNo .= $msec;
        return $waterNo;

    }
}