<?php
/**
 * Created by PhpStorm.
 * User: luzhouyu
 * Date: 18/7/27
 * Time: 上午9:44
 */

namespace Uniondrug\PolicySdk;

use Phalcon\Di\ServiceProviderInterface;

class SdkServiceProvider implements ServiceProviderInterface
{
    public function register(\Phalcon\DiInterface $di)
    {
        $di->set(
            'policySdk',
            function () {
                return new PolicySdk();
            }
        );
    }
}