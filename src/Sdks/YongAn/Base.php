<?php

namespace Uniondrug\PolicySdk\Sdks\YongAn;

use Uniondrug\PolicySdk\Sdk;
use Uniondrug\PolicySdk\sdks\YongAn\Modules\Insure;
use Uniondrug\PolicySdk\sdks\YongAn\Modules\Surrender;

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
}