<?php

namespace Uniondrug\PolicySdk\Sdks\RenBaoJson;

use Uniondrug\PolicySdk\Sdk;
use Uniondrug\PolicySdk\Sdks\RenBaoJson\Modules\Insure;
use Uniondrug\PolicySdk\Sdks\RenBaoJson\Modules\Surrender;

class Base extends Sdk
{
    /*
     * 投保
     */
    use Insure;

    /*
     * 退保
     */
    use Surrender;
}