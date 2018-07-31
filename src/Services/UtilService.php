<?php
/**
 * UtilService.php
 *
 */
namespace Uniondrug\PolicySdk\Services;

class UtilService
{
    //判断是否为身份证
    public function checkIdCardNumber($IDCard)
    {
        $IDCard = strtoupper($IDCard);
        if (strlen($IDCard) == 18) {
            return $this->check18IDCard($IDCard);
        } elseif ((strlen($IDCard) == 15)) {
            $IDCard = $this->convertIDCard15to18($IDCard);
            return $this->check18IDCard($IDCard);
        } else {
            return false;
        }
    }

    //获取身份证中的性别
    public function getSexByIdCard($IDCard) {
        if (strlen($IDCard) == 18) {
            return $this->check18sex($IDCard);
        } elseif ((strlen($IDCard) == 15)) {
            return $this->check15sex($IDCard);
        } else {
            return false;
        }
    }

    //获取身份证中的出身年月日
    public function getBirthByIdCard($IDCard) {
        if (strlen($IDCard) == 18) {
            return $this->check18birthday($IDCard);
        } elseif ((strlen($IDCard) == 15)) {
            $IDCard = $this->convertIDCard15to18($IDCard);
            return $this->check18birthday($IDCard);
        } else {
            return false;
        }
    }

    private function check18birthday($IDCard) {
        $tyear= substr($IDCard,6,4);
        $tmonth= substr($IDCard,10,2);
        $tday= substr($IDCard,12,2);
        return $tyear."-".$tmonth."-".$tday;
    }

    private function check18sex($IDCard) {
        $num = intval(substr($IDCard,16,1));
        if(($num%2) == 0) {
            return "02";
        } else {
            return "01";
        }
    }
    private function check15sex($IDCard) {
        $num = intval(substr($IDCard,14,1));
        if(($num%2) == 0) {
            return "02";
        } else {
            return "01";
        }
    }


    //计算身份证的最后一位验证码,根据国家标准GB 11643-1999
    private function calcIDCardCode($IDCardBody)
    {
        if (strlen($IDCardBody) != 17) {
            return false;
        }

        //加权因子
        $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);

        //校验码对应值
        $code = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
        $checksum = 0;

        for ($i = 0; $i < strlen($IDCardBody); $i++) {
            $checksum += substr($IDCardBody, $i, 1) * $factor[$i];
        }

        return $code[$checksum % 11];
    }

    // 将15位身份证升级到18位
    private function convertIDCard15to18($IDCard)
    {
        if (strlen($IDCard) != 15) {
            return false;
        } else {
            // 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
            if (array_search(substr($IDCard, 12, 3), array('996', '997', '998', '999')) !== false) {
                $IDCard = substr($IDCard, 0, 6) . '18' . substr($IDCard, 6, 9);
            } else {
                $IDCard = substr($IDCard, 0, 6) . '19' . substr($IDCard, 6, 9);
            }
        }
        $IDCard = $IDCard . $this->calcIDCardCode($IDCard);
        return $IDCard;
    }

    // 18位身份证校验码有效性检查
    private function check18IDCard($IDCard)
    {
        if (strlen($IDCard) != 18) {
            return false;
        }

        $IDCardBody = substr($IDCard, 0, 17); //身份证主体
        $IDCardCode = strtoupper(substr($IDCard, 17, 1)); //身份证最后一位的验证码

        if ($this->calcIDCardCode($IDCardBody) != $IDCardCode) {
            return false;
        } else {
            return true;
        }
    }
}
