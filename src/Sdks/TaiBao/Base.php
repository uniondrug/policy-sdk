<?php

namespace Uniondrug\PolicySdk\Sdks\TaiBao;

use Uniondrug\PolicySdk\Sdk;
use Uniondrug\PolicySdk\Sdks\TaiBao\Modules\Insure;
use Uniondrug\PolicySdk\Sdks\TaiBao\Modules\Surrender;

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