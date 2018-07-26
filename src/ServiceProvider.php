<?php
/**
 * Created by PhpStorm.
 * User: luzhouyu
 * Date: 18/7/26
 * Time: 上午12:26
 */

namespace Uniondrug\PolicyService;


use Pails\Plugins\ApiResponse;
use Uniondrug\PolicyService\Plugins\Logger;

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
    }
}