<?php

namespace Uniondrug\PolicySdk\Sdks\RenBaoCar;

use Uniondrug\PolicySdk\Sdk;
use Uniondrug\PolicySdk\Sdks\RenBaoCar\Modules\CheckQuote;
use Uniondrug\PolicySdk\Sdks\RenBaoCar\Modules\CollectInfo;
use Uniondrug\PolicySdk\Sdks\RenBaoCar\Modules\DefaultQuote;
use Uniondrug\PolicySdk\Sdks\RenBaoCar\Modules\FreeQuote;
use Uniondrug\PolicySdk\Sdks\RenBaoCar\Modules\Notify;
use Uniondrug\PolicySdk\Sdks\RenBaoCar\Modules\PolicyChange;
use Uniondrug\PolicySdk\Sdks\RenBaoCar\Modules\PolicyPush;
use Uniondrug\PolicySdk\Sdks\RenBaoCar\Modules\PolicyReading;
use Uniondrug\PolicySdk\Sdks\RenBaoCar\Modules\SeatFollow;
use Uniondrug\PolicySdk\Sdks\RenBaoCar\Modules\SuppleQuote;
use Uniondrug\PolicySdk\Sdks\RenBaoCar\Modules\Verify;

class Base extends Sdk
{
    /*
    * 人保接口只能支持最大长度32位
    */
    const water_no_length = 32;

    /*
     * 投保
     */
    use CheckQuote;

    /*
     * 收集信息
     */
    use CollectInfo;

    /*
     * 报价
     */
    use DefaultQuote;

    /*
     * 自由报价
     */
    use FreeQuote;

    /*
     * 电子阅读
     */
    use PolicyReading;

    /*
     * 坐席跟进
     */
    use SeatFollow;

    /*
     * 补充报价
     */
    use SuppleQuote;

    /*
     * 验证码
     */
    use Verify;

    /*
     * 投保单回推
     */
    use PolicyPush;

    /*
     * 投保单回推
     */
    use Notify;

    /*
     * 批改回传
     */
    use PolicyChange;

    /**
     * 创建唯一流水号
     */
    public function createUniqueWaterNo()
    {
        return parent::createUniqueWaterNo(self::water_no_length);
    }

    /**
     * 请求人保接口
     * @param $xml_content
     * @return array|mixed|\SimpleXMLElement
     */
    public function getCurl($request_content, $name, $transactionNo,$requestType)
    {
        $request_content = convert_encoding($request_content,"GBK");
        try {
            $xml_content = $this->getHead($transactionNo,$requestType);
            $xml_content .= $request_content;
            $xml_content .= '<Sign>' . $this->getSign($request_content) . '</Sign>
            </Package>
        </PackageList>';
            $url = $this->config->url;
            $this->logger->{$name}()->info("保司请求报文:" . $xml_content);
            $header = ['Content-Type: text/xml;charset=GBK'];
            $result = $this->curl_https($url, $xml_content, $header, $name, '120');
            $this->logger->{$name}()->info("保司响应报文:" . $result);
            $pattern = "/<Response>.*?<\/Response>/is";
            preg_match($pattern,$result,$data);
            //验签
            $resultArray = xml_to_array($result, 'GBK');
            $this->checkSign($data['0'],$resultArray['Package']['Sign']);
            if(in_array($requestType,[101106,101110,101105])){
                $resultArray['data'] = $this->xmlWithAttribute($result);
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return $resultArray;
    }

    //加签验证
    private function getSign($request)
    {
        $privateKey = file_get_contents(__DIR__ . "/rsa/our/rsa_private_key.pem");
        $pkeyid = openssl_get_privatekey($privateKey);
        if (empty($pkeyid)) {
            throw new \Exception('获取签名失败!');
        }
        $verify = openssl_sign(trim($request), $signature, $pkeyid, OPENSSL_ALGO_MD5);
        openssl_free_key($pkeyid);
        return $this->urlsafe_b64encode($signature);
    }

    //签名验证
    private function checkSign($response, $sign)
    {
        $publicKey = file_get_contents(__DIR__ . "/rsa/their/rsa_public_key.pem");
        $pkeyid = openssl_get_publickey($publicKey);
        if (empty($pkeyid)) {
            throw new \Exception('获取签名失败!');
        }
        $sign = $this->urlsafe_b64decode($sign);
        $ret = openssl_verify($response, $sign, $pkeyid, OPENSSL_ALGO_MD5);
        openssl_free_key($pkeyid);
        if ($ret != 1) {
            throw new \Exception('验签失败!');
        }
        return true;
    }

    //组装头部报文
    private function getHead($transactionNo,$requestType)
    {
        $head_content = '<?xml version="1.0" encoding="GBK"?>
        <PackageList>
            <Package>
                <Header>
                    <Version>2</Version>
                    <RequestType>' . $requestType . '</RequestType>
                    <InsureType>100</InsureType>
                    <SessionId>' . $transactionNo . '</SessionId>
                    <SellerId></SellerId>
                    <From>YL</From>
                    <SendTime>' . date('Y-m-d H:i:s', time()) . '</SendTime>
                    <Status>100</Status>
                    <ErrorMessage></ErrorMessage>
                </Header>';

        return $head_content;
    }

    //TagsList标签内容转换
    private function xmlWithAttribute($result)
    {
        $pattern = "/<Response>.*?<\/Response>/is";
        preg_match($pattern,$result,$data);
        $dom = new \DOMDocument();
        $dom->loadXML(convert_encoding($data[0]));
        $root = $dom->documentElement;
        $tags=$root->getElementsByTagName('Tags');
        foreach ($tags as $key=>$tag){
            $definitions = $tag->getElementsByTagName('Tag');
            foreach ($definitions as $item => $definition){
                foreach ($definition->getElementsByTagName('Definition') as $definition){
                    $child[$item][$definition->getAttribute('name')] = $definition->nodeValue;
                }
            }
            $arr[$tag->getAttribute('type')] = $child;
            $child=[];
        }
        return $arr;

    }
    function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key=>$val)
        {
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }
    /*
     * 验证回推投保单,回调保单的签名
     */
    private function policySign($response, $sign){
        $response = str_replace("\r","",$response);
        $response = convert_encoding($response,"GBK");
        $data['status']=100;
        $publicKey = file_get_contents(__DIR__ . "/rsa/their/rsa_public_key.pem");
        $pkeyid = openssl_get_publickey($publicKey);
        if (empty($pkeyid)) {
            $data['status'] = 500;
            $data['error'] =  '获取签名失败!';
            return $data;
        }
        $sign = $this->urlsafe_b64decode($sign);
        $ret = openssl_verify($response,$sign, $pkeyid, OPENSSL_ALGO_MD5);
        openssl_free_key($pkeyid);
        if ($ret != 1) {
            $data['status'] = 500;
            $data['error'] =  '验签失败!';
            return $data;
        }
        return $data;
    }

    /*
     * 给返回保司的报文加签
     */
    public function addSign($result){
        $pattern = "/<Request>.*?<\/Request>/is";
        preg_match($pattern,$result,$data);
        $request_content = convert_encoding($data[0],"GBK");
        $result .= '<Sign>' . $this->setSign($request_content) . '</Sign>
            </Package>
        </PackageList>';
        return $result;
    }

    /*
     * 加签
     */
    private function setSign($request)
    {
        $privateKey = file_get_contents(__DIR__ . "/rsa/our/rsa_private_key.pem");
        $pkeyid = openssl_get_privatekey($privateKey);
        $verify = openssl_sign(trim($request), $signature, $pkeyid, OPENSSL_ALGO_MD5);
        openssl_free_key($pkeyid);
        return $this->urlsafe_b64encode($signature);
    }
    /*
     * base64 加密 url安全字符串编码
     */
    public function urlsafe_b64encode($string) {
        $data = base64_encode($string);
        $data = str_replace(array('+','/','='),array('-','_',''),$data);
        return $data;
    }
    /*
     * base64 解密 url安全字符串编码
     */
    public function urlsafe_b64decode($string) {
        $data = str_replace(array('-','_'),array('+','/'),$string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }
}