<?php

namespace Uniondrug\PolicySdk\Sdks\GuoShou;

use Uniondrug\PolicySdk\Sdk;
use Uniondrug\PolicySdk\Sdks\GuoShou\Modules\Insure;

class Base extends Sdk
{
    /*
     * 投保
     */
    use Insure;
}