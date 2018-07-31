<?php

namespace Uniondrug\PolicySdk\Plugins;

use Phalcon\Logger\Adapter\File as File;
use Uniondrug\Framework\Container;

/**
 * Class Logger
 * @package Uniondrug\PolicySdk\Plugins
 */
class Logger
{
    public $sdkName;

    /**
     * @var DiInterface
     */
    protected $_dependencyInjector;

    public function __construct($sdkName)
    {
        $this->sdkName = $sdkName;
    }

    /*
     * 自定义日志
     */
    public function __call($name, $arguments)
    {
        $logPath = $this->getDI() ? $this->getDI()->logPath() : './log';
        $date = date('Y-m-d');
        $dir = $logPath  . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . $this->sdkName . DIRECTORY_SEPARATOR;
        @mkdir($dir,0777, true);
        $logFile = $dir . $date . '.log';
        return new File($logFile);
    }

    /**
     * @return DiInterface
     */
    public function getDI()
    {
        if (!is_object($this->_dependencyInjector)) {
            $this->_dependencyInjector = Container::getDefault();
        }
        return $this->_dependencyInjector;
    }
}