<?php

namespace Uniondrug\PolicySdk;

use Phalcon\Di;
use Phalcon\Config;

/**
 * 投保容器
 * Class Container
 * @package Uniondrug\PolicySdk
 */
class Container extends Di\FactoryDefault implements ContainerInterface
{
    /**
     * Service Version
     */
    const VERSION = '1.0';

    /**
     * @var string
     */
    protected $basePath;

    /**
     * 保司唯一标识
     * @var string
     */
    protected $cooperation;

    /**
     * 保司配置
     * @var
     */
    protected $config;

    /**
     * Container constructor.
     * @param null $basePath
     */
    public function __construct($basePath = null)
    {
        parent::__construct();
        $basePath AND $this->setBasePath($basePath);

        // register service
        ServiceProvider::register();

        // register servicinstancees from providers.php
        $providers = (array) $this->getConfig('providers');
        $this->registerServices($providers);
    }

    /**
     * 实例化一个保司对象
     * @param $cooperation
     */
    public function instance($cooperation)
    {
        $cooperation AND $this->setCooperation($cooperation);
        try {
            $instance = $this->get("policy:{$cooperation}");
        } catch (\Exception $e) {
            throw new \Exception("保司对象实例化异常");
        }
        return $instance;
    }

    /**
     * Get the version number of pails.
     *
     * @return string
     */
    public function version()
    {
        return static::VERSION;
    }

    /**
     * 注册服务列表
     *
     * @param array $serviceProviders
     */
    public function registerServices($serviceProviders = [])
    {
        foreach ($serviceProviders as $key => $serviceProviderClass) {
            $this->setShared(
                $key,
                new $serviceProviderClass
            );
        }
    }

    /**
     * 注册保司唯一标识
     * @param $cooperation
     */
    public function setCooperation($cooperation)
    {
        $this->cooperation = $cooperation;
    }

    public function __get($name)
    {
        return $this->{$name};
    }

    /**
     * @param $basePath
     * @return $this
     */
    public function setBasePath($basePath)
    {
        $this->basePath = rtrim($basePath, '\/');
        return $this;
    }

    /**
     * @return string
     */
    public function logPath()
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'log';
    }

    /**
     * Helpers: Get the path to the application configuration files.
     *
     * @return string
     */
    public function configPath()
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'config';
    }

    /**
     * 获取配置. 自动注入到服务中
     *
     * @param      $section
     * @param null $key
     * @param null $defaultValue
     *
     * @return array|mixed
     */
    public function getConfig($section, $key = null, $defaultValue = null)
    {
        $serviceName = '___PAILS_CONFIG___' . $section;
        if ($this->has($serviceName)) {
            $service = $this->get($serviceName);
            if ($key) {
                return $service->get($key, $defaultValue);
            }
            return $service;
        }
        $configFile = $this->configPath() . DIRECTORY_SEPARATOR . $section . '.php';
        if (file_exists($configFile)) {
            // register a new config service
            $this->setShared($serviceName, Config::class);
            // instance the config service
            $config = require $configFile;
            $service = $this->get($serviceName, [$config]);
            if ($key) {
                return $service->get($key, $defaultValue);
            }
            return $service;
        } else {
            return $defaultValue;
        }
    }

    /**
     * Override DI's get() method, setEventsManager by default.
     *
     * {@inheritdoc}
     */
    public function get($name, $parameters = null)
    {
        $instance = parent::get($name, $parameters);
        if (is_object($instance) && $instance instanceof EventsAwareInterface) {
            if (!$instance->getEventsManager() && ($eventsManager = $this->getEventsManager())) {
                $instance->setEventsManager($eventsManager);
            }
        }
        return $instance;
    }

}