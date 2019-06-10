<?php

namespace Uniondrug\PolicySdk\Sdks\RenBaoDrug\Modules;

trait Upload
{
    public function upload(array $post)
    {
        $xml_content = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<PACKET>
    <RequestHead>
        <Uuid>' . $post['waterNo'] . '</Uuid>
        <Sender>' . $this->config->sender . '</Sender>
        <User>' . $this->config->user . '</User>
        <Password>' . $this->config->psssword . '</Password>
        <FlowinTime>' . date('Y-m-d H:i:s') . '</FlowinTime>
        <RequestType>' . $this->config->uploadRequestType . '</RequestType>
        <ServerVersion>' . $this->config->serverVersion . '</ServerVersion>
        <Token>' . $this->getToken($post) . '</Token>
    </RequestHead>
    <RequestBody>
		 <RegistNo>' . $post['registNo'] . '</RegistNo>
         <PictureList>'.$this->getFileList($post['fileInfo']).'</PictureList>
    </RequestBody>
</PACKET>';

        $this->logger->upload()->info("保司请求报文:" . convert_encoding($xml_content));
        $header = ['Content-Type: application/x-www-form-urlencoded'];;
        try {
            $result = $this->curl_https($this->config->uploadUrl, $xml_content, $header, __FUNCTION__,30);
            $this->logger->upload()->info("保司响应报文:" . convert_encoding($result));
            $resultArray = xml_to_array($result,'GBK');
        } catch (\Exception $e) {
            return $this->withError($e->getMessage());
        }
        if($resultArray['ResponseBody']['ErrorMessage'] != '0'){
            return $this->withError($resultArray['ResponseBody']['ErrorMessage']);
        }
        if(count($resultArray['ResponseBody']['PictureList']['Picture']) == count($resultArray['ResponseBody']['PictureList']['Picture'],1)){
            return $this->withData([
                'waterNo' => $resultArray['ResponseHead']['Uuid'],
                'registNo' => $resultArray['ResponseBody']['RegistNo'],
                'fileList' => [$resultArray['ResponseBody']['PictureList']['Picture']]
            ]);
        }else{
            return $this->withData([
                'waterNo' => $resultArray['ResponseHead']['Uuid'],
                'registNo' => $resultArray['ResponseBody']['RegistNo'],
                'fileList' => $resultArray['ResponseBody']['PictureList']['Picture']
            ]);
        }
    }

    //文件列表
    private function getFileList( array $fileInfo ){
        $data = '';
        foreach ($fileInfo as $file){
            $data .= '<Picture>
          <PageUrl>' . $file['requestUrl'] . '</PageUrl>
          <FileName>' . $file['fileName'] . '</FileName>
          <CreateTime>' . date('Y-m-d H:i:s') . '</CreateTime>
          <Remark>' . date('Y-m-d H:i:s') . '</Remark>
          <PageNo>' . $file['pageNo'] . '</PageNo>
          </Picture>';
        }
        return $data;
    }
    //Token
    private function getToken($post){
        $str = $post['registNo'];
        foreach ($post['fileInfo'] as $file){
            $str .= $file['requestUrl'];
        }
        return md5($str);
    }
}