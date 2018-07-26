<?php
namespace Uniondrug\PolicySdk\Providers;

use Uniondrug\PolicySdk\Injectable;
use Uniondrug\PolicySdk\InjectableTrait;
use Uniondrug\PolicySdk\Structs\Config;

abstract class AbstractCompanyProvider extends Injectable
{
    use InjectableTrait;
    /**
     * 保司的相关配置
     * @Config
     */
    public $config;

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