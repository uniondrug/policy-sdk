<?php
/**
 * Created by PhpStorm.
 * User: luzhouyu
 * Date: 18/7/26
 * Time: 下午10:40
 */

namespace Uniondrug\PolicySdk\Plugins;

use Uniondrug\PolicySdk\Injectable;

class PolicySdk extends Injectable
{
    /**
     * 实例化一个保司对象
     * @param $cooperation
     */
    public function instance($cooperation)
    {
        $cooperation AND $this->di->setCooperation($cooperation);
        try {
            $instance = $this->di->get("policy:{$cooperation}");
        } catch (\Exception $e) {
            throw new \Exception("保司对象实例化异常");
        }
        return $instance;
    }
}