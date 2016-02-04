<?php namespace Menthol\Flexible;

class Config
{

    private $app;
    private $configPath;

    public function __construct($app, $configPath)
    {
        $this->app = $app;
        $this->configPath = $configPath;
    }

    public function get($key, $default = null)
    {
        return $this->app['config']->get('flexible.' . $key, $default);
    }
}
