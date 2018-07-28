<?php

namespace Uniondrug\PolicySdk\Plugins;

use Phalcon\Logger\Adapter\File as File;

/**
 * Class Logger
 * @package Uniondrug\PolicySdk\Plugins
 * @property \Phalcon\Di|\Phalcon\DiInterface                                                       $di
 */
class Logger
{
    public $sdkName;

    public function __construct($sdkName)
    {
        $this->sdkName = $sdkName;
    }

    /*
     * 自定义日志
     */
    public function __call($name, $arguments)
    {
        $logPath = $this->di ? $this->di->logPath() : './log';
        $date = date('Y-m-d');
        $dir = $logPath  . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . $this->sdkName . DIRECTORY_SEPARATOR;
        @mkdir($dir,0777, true);
        $logFile = $dir . $date . '.log';
        return new File($logFile);
    }
}