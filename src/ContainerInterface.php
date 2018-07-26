<?php
/**
 * Created by PhpStorm.
 * User: luzhouyu
 * Date: 18/7/25
 * Time: 下午10:46
 */

namespace Uniondrug\PolicyService;

use Phalcon\DiInterface;

interface ContainerInterface extends DiInterface
{
    public function version();

    public function setBasePath($basePath);

    public function logPath();

    public function configPath();

    public function getConfig($section, $key = null, $defaultValue = null);

}