<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

// Composer autoloader

require __DIR__ . '/../vendor/autoload.php';

// Boot Aspect Mock

$kernel = \AspectMock\Kernel::getInstance();
$src = __DIR__ . '/../src';
$eloquent = __DIR__ . '/../vendor/illuminate/database/Illuminate/Database/Eloquent';

$kernel->init([
    'debug' => true,
    'cacheDir' => '/tmp/flexible',
    'includePaths' => [$src, $eloquent]
]);

// Boot the Eloquent component

$capsule = new \Illuminate\Database\Capsule\Manager();

$capsule->addConnection(array(
    'driver' => 'sqlite',
    'database' => __DIR__ . '/database/testing.sqlite',
    'prefix' => '',
));

$capsule->bootEloquent();
