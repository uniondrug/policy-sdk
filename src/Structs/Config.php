<?php
namespace Uniondrug\PolicySdk\Structs;

/**
 * 保司的相关配置类
 * Class Config
 * @package Uniondrug\PolicySdk\Structs
 */
class Config
{
    public function __construct($config)
    {
        foreach ($config as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function __set($name, $value)
    {
        $this->{$name} = $value;
    }

    public function __get($name)
    {
        return $this->{$name};
    }
}