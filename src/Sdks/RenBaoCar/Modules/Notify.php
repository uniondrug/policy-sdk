<?php

namespace Uniondrug\PolicySdk\Sdks\RenBaoCar\Modules;

use mysql_xdevapi\Exception;

trait Notify{
    public function notify($result){
        //将保司推送的xml投保单 转为数组
        $resultArray = xml_to_array($result,'GB2312');
        //进行验签 如果通过 进行存储返回正确报文  如果失败 返回错误报文
        $pattern = "/<Response>.*?<\/Response>/is";
        preg_match($pattern,$result,$data);
        //验签
        $checkSign = $this->policySign($data['0'],$resultArray['Package']['Sign']);
        $data = $resultArray["Package"]["Header"];
        if($checkSign['status'] != 100 && isset($checkSign['status'])){
            $data['Status']=$checkSign['status'];
            $data['ErrorMessage']=$checkSign['error'];
            $data['resultArray'] = $resultArray;
            return $this->withData($data);
        }
        $data['Status'] = 100;
        $data['ErrorMessage'] = "成功";
        $data['resultArray'] = $resultArray;
        return $this->withData($data);

    }

}
