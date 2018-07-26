<?php

namespace Uniondrug\PolicyService\Plugins;

use Phalcon\Logger\Adapter\File as File;
use Uniondrug\PolicyService\Container;

class Logger
{
    /*
     * 自定义日志
     */
    public function __call($name, $arguments)
    {
        $di = Container::getDefault();
        $date = date('Y-m-d');
        $dir = $di->logPath() . DIRECTORY_SEPARATOR . $di->cooperation . DIRECTORY_SEPARATOR . $date . DIRECTORY_SEPARATOR;
        @mkdir($dir,0777,true);
        $logFile = $dir . $name . '.log';
        return new File($logFile);
    }
}