<?php

namespace Uniondrug\PolicySdk\Plugins;

use Phalcon\Logger\Adapter\File as File;
use Uniondrug\PolicySdk\Injectable;

class Logger extends Injectable
{
    /*
     * 自定义日志
     */
    public function __call($name, $arguments)
    {
        $date = date('Y-m-d');
        $dir = $this->di->logPath() . DIRECTORY_SEPARATOR . $this->di->cooperation . DIRECTORY_SEPARATOR . $date . DIRECTORY_SEPARATOR;
        @mkdir($dir,0777,true);
        $logFile = $dir . $name . '.log';
        return new File($logFile);
    }
}