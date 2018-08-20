<?php

namespace Uniondrug\PolicySdk\Sdks\TaiBaoYiWai;

use Uniondrug\PolicySdk\Sdk;
use Uniondrug\PolicySdk\Sdks\TaiBaoYiWai\Modules\Insure;
use Uniondrug\PolicySdk\Sdks\TaiBaoYiWai\Modules\Surrender;

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