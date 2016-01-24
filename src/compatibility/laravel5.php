<?php

spl_autoload_register(function($class) {
    $class_map = [
        'Menthol\Flexible\Contracts\Arrayable' => __DIR__.'/laravel5/Arrayable.php',
    ];

    if (isset($class_map[$class])) {
        require_once $class_map[$class];
    }
});
