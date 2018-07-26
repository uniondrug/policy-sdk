<?php
/**
 * Created by PhpStorm.
 * User: luzhouyu
 * Date: 18/7/26
 * Time: 上午12:26
 */

namespace Uniondrug\PolicySdk;


use Uniondrug\PolicySdk\Plugins\ApiResponse;
use Uniondrug\PolicySdk\Plugins\Logger;
use Uniondrug\PolicySdk\Plugins\PolicySDK;

class ServiceProvider
{
    public static function register()
    {
        $di = Container::getDefault();

        /*
         * 日志服务
         */
        $di->setShared(
            'logger',
            function () {
                return new Logger();
            }
        );

        /*
         * 响应服务
         */
        $di->setShared(
            'apiResponse',
            function () {
                return new ApiResponse();
            }
        );

        /*
         * 保司实例化服务
         */
        $di->setShared(
            'policySDK',
            function () {
                return new PolicySDK();
            }
        );
    }
}