<?php

namespace Uniondrug\PolicySdk\Sdks\TaiBaoZeRen;

use Uniondrug\PolicySdk\Sdk;
use Uniondrug\PolicySdk\Sdks\TaiBaoZeRen\Modules\Insure;
use Uniondrug\PolicySdk\Sdks\TaiBaoZeRen\Modules\Surrender;

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