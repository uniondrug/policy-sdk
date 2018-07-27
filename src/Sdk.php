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
    protected $config;

    /**
     * 工具
     * @var
     */
    protected $utilService;

    /**
     * 日志服务
     * @var Logger
     */
    protected $logger;


    public function __construct($sdkName)
    {
        $this->logger = new Logger($sdkName);
        $this->utilService = new UtilService();
    }

    /*
     * 保司配置初始化
     */
    public function setConfig($config) {
        $this->config = new Config($config);
    }

    /**
     * 投保
     * @param array $post   入参
     * @param array $extResponse 额外的响应参数
     * @return mixed
     */
    public abstract function insure(array $post,&$extResponse = []);

    /**
     * 退保
     * @param array $post
     * @return mixed
     */
    public abstract function surrender(array $post);

    /**
     * 失败
     * @param string $error
     * @param int $errno
     * @return array
     */
    public function withError(string $error, $errno = 1)
    {
        return [
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
    public function withData(array $data, $errno = 0)
    {
        return [
            'errno' => (string) $errno,
            'error' => '',
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
        $this->logger->{$name}()->info("保司接口响应时间(秒):".$info['total_time']);
        $http_code = $info['http_code'];
        while ($times && in_array($http_code,['0','100'])) {
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