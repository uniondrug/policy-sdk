<?php
/**
 * Created by PhpStorm.
 * User: luzhouyu
 * Date: 18/7/27
 * Time: 上午9:44
 */

namespace Uniondrug\PolicySdk;

/**
 * 保司SDK
 * Class PolicySdk
 * @package Uniondrug\PolicySdk
 */
class PolicySdk
{
    private $config;

    public function __construct()
    {
        $sdkConfigFile = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config.php';
        $this->config = require_once $sdkConfigFile;
    }

    /**
     * SDK模块列表
     * @var array
     */
    private static $_modules = [];

    /**
     * 实例化一个保司对象
     * @param $SDK
     */
    public function instance($SDK)
    {
        $key = "Sdk:" . strtoupper($SDK);
        //  从上个实例中读取
        if (isset(self::$_modules[$key])) {
            return self::$_modules[$key];
        }
        //  检查定义
        $name = $this->config[$key];
        $class = __NAMESPACE__ . '\\Sdks\\' . $name . '\\Base';
        try {
            $instance = new $class($name);
            self::$_modules[$key] = $instance;
            return self::$_modules[$key];
        } catch (\Throwable $e) {
        }
        // 3. 未定义的SDK服务
        throw new \Exception("SDK包中未找到'{$SDK}'定义");
    }

    /**
     * 获取配置
     */
    public function config()
    {
        $keys = array_keys($this->config);
        foreach ($keys as $val) {
            $data[] = explode(":", $val)[1];
        }
        return $data;
    }
}

