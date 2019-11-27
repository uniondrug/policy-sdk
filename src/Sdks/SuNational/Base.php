<?php

namespace Uniondrug\PolicySdk\Sdks\SuNational;

use Uniondrug\PolicySdk\Sdk;
use Uniondrug\PolicySdk\Sdks\SuNational\Modules\SuClaim;
use Uniondrug\PolicySdk\Sdks\SuNational\Modules\FeeUpload;
use Uniondrug\PolicySdk\Sdks\SuNational\Modules\CostSettle;
use Uniondrug\PolicySdk\Sdks\SuNational\Modules\CaseRevocation;
class Base extends Sdk
{
    /*
     * 就医登记
     */
    use SuClaim;
    /*
     * 多票据费用上传
     */
    use FeeUpload;
    /*
     * 多票据费用结算
     */
    use CostSettle;
    /**
     * 案件撤销
     */
    use CaseRevocation;
}