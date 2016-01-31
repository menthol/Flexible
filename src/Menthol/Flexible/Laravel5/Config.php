<?php namespace Menthol\Flexible\Laravel5;

use Menthol\Flexible\Config as ConfigInterface;

class Config implements ConfigInterface
{

    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function get($key, $default = null)
    {
        return $this->app['config']->get('flexible.' . $key, $default);
    }
}