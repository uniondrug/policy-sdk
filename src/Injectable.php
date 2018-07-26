<?php
/**
 * Created by PhpStorm.
 * User: luzhouyu
 * Date: 18/7/26
 * Time: 下午10:47
 */

namespace Uniondrug\PolicySdk;

/**
 * Class Injectable
 * @package Uniondrug\PolicySdk
 * @property \Uniondrug\PolicySdk\Plugins\Logger $logger
 * @property \Uniondrug\PolicySdk\Plugins\ApiResponse $apiRespons
 * @property \Uniondrug\PolicySdk\Plugins\PolicySDK $policySDK
 */
class Injectable
{
    use InjectableTrait;
}