<?php namespace Menthol\Flexible\Utilities;

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\ClassLoader\ClassMapGenerator;

class ModelDiscovery {

    static public function discover($directory) {
        $models = [];

        foreach (ClassMapGenerator::createMap($directory) as $model => $path) {
            $class_parents = @class_parents($model);
            if ($class_parents && in_array(Model::class, $class_parents, true)) {
                $models[] = $model;
            }
        }

        return $models;
    }
}
