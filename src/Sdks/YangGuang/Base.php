<?php

namespace Uniondrug\PolicySdk\Sdks\YangGuang;

use Uniondrug\PolicySdk\Sdk;
use Uniondrug\PolicySdk\Sdks\YangGuang\Modules\Epolicy;
use Uniondrug\PolicySdk\Sdks\YangGuang\Modules\Insure;
use Uniondrug\PolicySdk\Sdks\YangGuang\Modules\Surrender;

class Base extends Sdk
{
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
}