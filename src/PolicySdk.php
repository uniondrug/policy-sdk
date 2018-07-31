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
    /**
     * SDK模块列表
     * @var array
     */
    private static $_modules = [];

    /**
     * 实例化一个保司对象
     * @param $cooperation
     */
    public function instance($cooperation)
    {
        $key = "Sdk:".strtoupper($cooperation);
        //  从上个实例中读取
        if (isset(self::$_modules[$key])) {
            return self::$_modules[$key];
        }
        //  检查定义
        $sdkConfigFile = __DIR__ . DIRECTORY_SEPARATOR . 'Configs' . DIRECTORY_SEPARATOR . 'sdk.php';
        if (!file_exists($sdkConfigFile)) {
            throw new \Exception("sdk配置文件丢失");
        }
        $config = require_once $sdkConfigFile;
        $class = __NAMESPACE__.'\\Modules\\'.$config[$key];
        try {
            $instance = new $class();
            self::$_modules[$key] = $instance;
            return self::$_modules[$key];
        } catch(\Throwable $e) {
        }
        // 3. 未定义的SDK服务
        throw new \Exception("SDK包中未找到'{$cooperation}'定义");
    }

    /**
     * 获取配置
     */
    public function config()
    {
        $sdkConfigFile = __DIR__ . DIRECTORY_SEPARATOR . 'Configs' . DIRECTORY_SEPARATOR . 'sdk.php';
        $config = require_once $sdkConfigFile;
        $keys = array_keys($config);
        foreach ($keys as $val) {
            $data[] = explode(":",$val)[1];
        }
        return $data;
    }
}

