<?php

namespace Uniondrug\PolicySdk\Sdks\TaiBao;

use Uniondrug\PolicySdk\Sdk;
use Uniondrug\PolicySdk\Sdks\TaiBao\Modules\Insure;
use Uniondrug\PolicySdk\Sdks\TaiBao\Modules\Surrender;

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

}