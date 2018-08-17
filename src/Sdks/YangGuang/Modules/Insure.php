<?php

namespace Uniondrug\PolicySdk\Sdks\YangGuang\Modules;

trait Insure
{
    public function insure(array $post, &$extResponse = [])
    {
        $xml = '<?xml version="1.0" encoding="GBK"?>
        <INSURENCEINFO>
          <USERNAME>' . $this->config->user . '</USERNAME>
          <PASSWORD>' . $this->config->password . '</PASSWORD>
          <ORDER>
            <ORDERID>'. $post['waterNo'] .'</ORDERID>
            <POLICYINFO>
              <SERIALNO>1</SERIALNO>
              <OPERATOR/>
              <POLICYNO/>
              <PRODUCTCODE>'. $post['riskCode'] .'</PRODUCTCODE>
              <PLANCODE/>
              <AGREEMENTNO>' . $this->config->protocol . '</AGREEMENTNO>
              <INSURDATE>'. $post['inputDate'] .'</INSURDATE>
              <INSURSTARTDATE>'. $post['startDate'] .'</INSURSTARTDATE>
              <INSURENDDATE>'. $post['endDate'] .'</INSURENDDATE>
              <MULT>1</MULT>
              <PREMIUM>'. $post['totalPremium'] .'</PREMIUM>
              <AMOUNT>'.$post['sumAssured'].'</AMOUNT>
              <BENEFMODE>0</BENEFMODE>
              '. $this->getPolicyInfo($post['policyInfo'], $post['policyExt']) .'
              <CERTIFICATEID/>
              <PeopleNum>'. count($post['insuredList']) .'</PeopleNum>
              '. $this->getInsuredList($post['insuredList'], $post['insuredExt']) .'
              '. $this->getDynamic($post['dynamicDto'], $post['dynamicExt']) .'
            </POLICYINFO>
          </ORDER>
        </INSURENCEINFO>';
        $xml_content = convert_encoding($xml, 'gbk');
        $postData = array(
            'data' => $xml_content,
            'sign' => md5($this->config->token . $xml_content),
            'functionFlag' => 'INSURE',
            'interfaceFlag' => 'TCYG',
        );
        $this->logger->insure()->info("保司请求报文:" . $xml);
        $header = ['Content-Type: application/x-www-form-urlencoded'];;
        $postQuery = http_build_query($postData);
        try {
            $result = $this->curl_https($this->config->insure, $postQuery, $header, __FUNCTION__);
        } catch (\Exception $e) {
            return $this->withError($e->getMessage());
        }
        $this->logger->insure()->info("保司响应报文:" . $result);
        $resultObj = xml_to_array($result);
        $orderObj = $resultObj['ORDER'];
        $policyObj = $orderObj['POLICY'];
        if ($orderObj['@attributes']['RETURN'] != 'true') {
            $error = $orderObj['@attributes']['ERROR'] ?: "";
            $error = !$error ? $policyObj['ERROR']['@attributes']['INFO'] : "";
            return $this->withError($error);
        }
        $data = [
            'policyNo' => $policyObj['@attributes']['POLICYNO'],
            'transTime' => date("Y-m-d H:i:s")
        ];
        return $this->withData($data);
    }

    protected function getPolicyInfo($policyInfo, $extSchema = [])
    {
        return "<APPNTNAME>{$policyInfo['policyName']}</APPNTNAME>
                <APPNTSEX>". ($policyInfo['policySex'] == "01" ? 1 : 2) ."</APPNTSEX>
                <APPNTBIRTHDAY>". date("Y-m-d", strtotime($policyInfo['policyBirthday'])) ."</APPNTBIRTHDAY>
                <APPNTIDTYPE>". $this->convertIdentifyType($policyInfo['policyIdentifyType']) ."</APPNTIDTYPE>
                <APPNTIDNO>{$policyInfo['policyIdentifyNumber']}</APPNTIDNO>
                <APPNTMOBILE>{$policyInfo['policyMobile']}</APPNTMOBILE>
                <APPNTPHONE/>
                <APPNTEMAIL/>
                <APPNTADDRESS/>";
    }

    protected function getInsuredList($insuredList, $extSchema = [])
    {
        $data = '<INSUREDLIST>';
        foreach ($insuredList as $key => $value) {
            $data .= "<INSURED>
                        <INSUREDNAME>{$value['insuredName']}</INSUREDNAME>
                        <INSUREDSEX>". ($value['insuredSex'] == "01" ? 1 : 2)  ."</INSUREDSEX>
                        <INSUREDBIRTHDAY>". date("Y-m-d", strtotime($value['insuredBirthday'])) ."</INSUREDBIRTHDAY>
                        <INSUREDIDNO>{$value['insuredIdentifyNumber']}</INSUREDIDNO>
                        <INSUREDIDTYPE>". $this->convertIdentifyType($value['insuredIdentifyType']) ."</INSUREDIDTYPE>
                        <INSUREDMOBILE>{$value['insuredMobile']}</INSUREDMOBILE>
                        <RELATIONSHIP>99</RELATIONSHIP>
                    </INSURED>";
        }
        $data .= '</INSUREDLIST>';
        return $data;
    }

    public function getDynamic($dynamicDto, $dynamicExt = [])
    {
        $xml = "";
        if (in_array('ticketNo', $dynamicExt)) {
            $xml .= "<AIRLINENO>{$dynamicDto['ticketNo']}</AIRLINENO>";
        }
        if (in_array('trafficStartTime', $dynamicExt)) {
            $xml .= "<AIRLINEDATE>{$dynamicDto['trafficStartTime']}</AIRLINEDATE>";
        }
        if (in_array('departure', $dynamicExt)) {
            $xml .= "<ORIGIN>{$dynamicDto['departure']}</ORIGIN>";
        }
        if (in_array('destination', $dynamicExt)) {
            $xml .= "<DESTINATION>{$dynamicDto['destination']}</DESTINATION>";
        }
        return $xml;
    }

    /**
     * 证件类型转换
     * @param $identityType
     * @return int
     */
    protected function convertIdentifyType($identityType)
    {
        switch ($identityType) {
            //  身份证
            case "01":
                $type = "10";
                break;
            //  户口簿
            case "07":
                $type = "11";
                break;
            //  驾驶证
            case "06":
                $type = "12";
                break;
            //  军官证
            case "02":
                $type = "13";
                break;
            //  港澳通行证
            case "04":
                $type = "17";
                break;
            //  台湾通行证
            case "05":
                $type = "18";
                break;
            //  护照
            case "03":
                $type = "60";
                break;
            //  其它
            default:
                $type = "99";
                break;
        }
        return $type;
    }
}