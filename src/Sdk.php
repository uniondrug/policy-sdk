<?php
/**
 * Created by PhpStorm.
 * User: luzhouyu
 * Date: 18/7/27
 * Time: 上午9:41
 */

namespace Uniondrug\PolicySdk;

use Uniondrug\PolicySdk\Plugins\Logger;
use Uniondrug\PolicySdk\Services\UtilService;
use Uniondrug\PolicySdk\Structs\Config;

abstract class Sdk
{
    /**
     * 保司的相关配置
     * @Config
     */
    public $config;

    /**
     * 工具
     * @var
     */
    public $utilService;

    /**
     * 日志服务
     * @var Logger
     */
    public $logger;


    public function __construct($sdkName)
    {
        $this->logger = new Logger($sdkName);
        $this->utilService = new UtilService();
    }

    /*
     * 保司配置初始化
     */
    public function setConfig($config)
    {
        $this->config = new Config($config);
    }

    /**
     * 按照最大长度50位来处理
     * 固定的4位毫秒值 + 6位时间值
     * @param $length 要求长度
     * @return string 流水号
     */
    public function createUniqueWaterNo($length = 50)
    {
        list($usec, $sec) = explode(" ", microtime());
        $msec = str_pad(round($usec * 1000), 4, rand(0,9));
        //  剩下需要获取的长度
        $length -= (4 + 6);
        $rand = substr(uniqid(), 7) . rand(str_pad(1, 14, 0), str_pad(9, 14, 9));
        $waterNo = date('ymd') . substr(implode(NULL, array_map('ord', str_split($rand))), 0, $length);
        $waterNo .= $msec;
        return $waterNo;
    }

    /**
     * 失败
     * @param string $error
     * @param int $errno
     * @return array
     */
    public function withError($error = "", $errno = 1)
    {
        $error = $error ?: "未知异常,请联系管理员";
        return (object)[
            'errno' => (string) $errno,
            'error' => (string) $error,
        ];
    }

    /**
     * 成功
     * @param array $data
     * @param int $errno
     * @return array
     */
    public function withData($data = [], $errno = 0)
    {
        return (object)[
            'errno' => (string) $errno,
            'error' => 'Success',
            'data' => (object) $data,
        ];
    }

    /**
     * Http处理器
     * @param $url
     * @param $post
     * @param array $header
     * @param string $name
     * @param int $timeout
     * @param int $times
     * @return mixed
     * @throws \Exception
     */
    function curl_https($url, $post, $header = array(), $name = 'info', $timeout = 8, $times = 3)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        //日志记录
        $http_code = $info['http_code'];
        $this->logger->{$name}()->info("保司接口响应时间(秒):".$info['total_time']);
        $this->logger->{$name}()->info("保司接口响应状态码:". $http_code);
        while ($times && $http_code != 200) {
            $times--;
            return $this->curl_https($url, $post, $header, $name , $timeout, $times);
        }
        if (!$times) {
            $msg = "核心接口连接失败";
            $this->logger->{$name}()->info($msg.",Http状态码:".$http_code);
            throw new \Exception($msg);
        }
        return $response;
    }
}