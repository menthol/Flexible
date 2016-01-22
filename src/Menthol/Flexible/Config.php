<?php namespace Menthol\Flexible;

interface Config
{
    public function get($key, $default = null);
}
