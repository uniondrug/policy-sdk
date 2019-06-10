<?php

namespace Uniondrug\PolicySdk\Sdks\RenBaoDrug;

use Uniondrug\PolicySdk\Sdk;
use Uniondrug\PolicySdk\Sdks\RenBaoDrug\Modules\Claim;
use Uniondrug\PolicySdk\Sdks\RenBaoDrug\Modules\Insure;
use Uniondrug\PolicySdk\Sdks\RenBaoDrug\Modules\Upload;

class Base extends Sdk
{
    /*
    * 人保接口只能支持最大长度32位
    */
    const water_no_length = 32;

    /*
     * 投保
     */
    use Insure;
    /*
     * 理赔
     * */
    use Claim;
    /*
     * 小微理赔影像导入接口
     * */
    use Upload;


    public function createUniqueWaterNo()
    {
        return parent::createUniqueWaterNo(self::water_no_length);
    }
    /**
     * 证件类型转换
     * @param $identityType
     * @return int
     */
    public function convertIdentifyType($identityType)
    {
        switch ($identityType) {
            //  身份证
            case "01":
                $type = "01";
                break;
            //  军官证
            case "02":
                $type = "04";
                break;
            //  护照
            case "03":
                $type = "03";
                break;
            //  港澳通行证
            case "04":
                $type = "10";
                break;
            //  台湾通行证
            case "05":
                $type = "09";
                break;
            //  驾驶证
            case "06":
                $type = "05";
                break;
            //  出生证
            case "07":
                $type = "02";
                break;
            //  外国人居留证
            case "08":
                $type = "16";
                break;
            //  组织机构代码
            case "09":
                $type = "31";
                break;
            default:
                $type = "99";
                break;
        }
        return $type;
    }
}